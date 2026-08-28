<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Cheque;
use App\Models\Deposit;
use App\Models\FormStock;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DashboardMetrics
{
    public function __construct(
        public CarbonInterface $from,
        public CarbonInterface $to,
    ) {}

    public static function forRange(string $range = 'month'): self
    {
        $now = Carbon::now();
        return match ($range) {
            'today' => new self($now->copy()->startOfDay(), $now->copy()->endOfDay()),
            'week'  => new self($now->copy()->startOfWeek(), $now->copy()->endOfWeek()),
            default => new self($now->copy()->startOfMonth(), $now->copy()->endOfMonth()),
        };
    }

    public static function forDates(?string $from, ?string $to): self
    {
        if ($from && $to) {
            return new self(Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay());
        }
        return self::forRange('month');
    }

    /** @return array{from: Carbon, to: Carbon} */
    protected function previousPeriod(): array
    {
        $lengthDays = $this->from->diffInDays($this->to) + 1;
        $to = $this->from->copy()->subDay()->endOfDay();
        $fromPrev = $to->copy()->subDays($lengthDays - 1)->startOfDay();
        return ['from' => $fromPrev, 'to' => $to];
    }

    public function collections(): array
    {
        $base = TransactionLog::whereBetween('transacted_at', [$this->from, $this->to])
            ->where('status', '!=', 'Cancelled');

        $total = (float) (clone $base)->sum('amount');
        $count = (clone $base)->count();

        $prev = $this->previousPeriod();
        $prevTotal = (float) TransactionLog::whereBetween('transacted_at', [$prev['from'], $prev['to']])
            ->where('status', '!=', 'Cancelled')->sum('amount');
        $deltaPct = $prevTotal > 0 ? round((($total - $prevTotal) / $prevTotal) * 100, 1) : null;

        return ['total' => $total, 'count' => $count, 'deltaPct' => $deltaPct];
    }

    public function disbursed(): array
    {
        $base = Cheque::whereBetween('cheque_date', [$this->from, $this->to])
            ->where('status', 'Issued');

        return ['total' => (float) (clone $base)->sum('amount'), 'count' => (clone $base)->count()];
    }

    public function cashPosition(): array
    {
        $accounts = BankAccount::all();

        $total = $accounts->sum(function (BankAccount $acc) {
            $in = (float) TransactionLog::whereHas('deposit', fn ($q) => $q->where('bank_account_id', $acc->id))
                ->sum('amount');
            $out = (float) Cheque::where('bank_account_id', $acc->id)->where('status', 'Issued')->sum('amount');
            return (float) $acc->opening_balance + $in - $out;
        });

        return ['total' => (float) $total, 'accounts' => $accounts->count()];
    }

    public function exceptions(): array
    {
        $cheques = Cheque::where('recon_status', 'failed')
            ->orderByDesc('updated_at')->limit(6)->get()
            ->map(fn ($c) => ['type' => 'bounced-cheque', 'label' => 'Cheque #' . $c->check_number . ' · ' . $c->pay_to_order_of, 'amount' => (float) $c->amount, 'at' => $c->updated_at]);

        $logs = TransactionLog::where('recon_status', 'failed')
            ->orderByDesc('updated_at')->limit(6)->get()
            ->map(fn ($l) => ['type' => 'failed-payment', 'label' => $l->serial_number . ' · ' . $l->payee, 'amount' => (float) $l->amount, 'at' => $l->updated_at]);

        $items = $cheques->concat($logs)
            ->sortByDesc('at')
            ->take(6)
            ->map(fn ($item) => ['type' => $item['type'], 'label' => $item['label'], 'amount' => $item['amount']])
            ->values()->all();
        $count = Cheque::where('recon_status', 'failed')->count() + TransactionLog::where('recon_status', 'failed')->count();

        return ['count' => $count, 'items' => $items];
    }

    public function trend(): array
    {
        $rows = TransactionLog::whereBetween('transacted_at', [$this->from, $this->to])
            ->where('status', '!=', 'Cancelled')
            ->selectRaw('date(transacted_at) as d, sum(amount) as amt, count(*) as cnt')
            ->groupBy('d')->orderBy('d')->get();

        $points = $rows->map(fn ($r) => ['date' => $r->d, 'amount' => (float) $r->amt, 'txns' => (int) $r->cnt])->all();
        $total = array_sum(array_column($points, 'amount'));
        $avg = count($points) ? $total / count($points) : 0.0;

        $peak = ['date' => '', 'amount' => 0.0];
        foreach ($points as $p) {
            if ($p['amount'] >= $peak['amount']) $peak = ['date' => $p['date'], 'amount' => $p['amount']];
        }

        return ['points' => $points, 'total' => (float) $total, 'avg' => (float) $avg, 'peak' => $peak];
    }

    public function reconciliation(): array
    {
        $liveLogs = TransactionLog::where('status', '!=', 'Cancelled');
        $logTotal = (clone $liveLogs)->count();
        $logDone  = (clone $liveLogs)->where('recon_status', 'completed')->count();

        $issued = Cheque::where('status', 'Issued');
        $chqTotal = (clone $issued)->count();
        $chqDone  = (clone $issued)->where('recon_status', 'completed')->count();

        return [
            'depositsMatchedPct' => $logTotal ? (int) round($logDone / $logTotal * 100) : 0,
            'chequesMatchedPct'  => $chqTotal ? (int) round($chqDone / $chqTotal * 100) : 0,
        ];
    }

    public function paymentMethods(): array
    {
        $base = TransactionLog::whereBetween('transacted_at', [$this->from, $this->to])
            ->where('status', '!=', 'Cancelled');

        $methods = (clone $base)
            ->selectRaw('payment_method as method, count(*) as cnt, sum(amount) as amt')
            ->groupBy('payment_method')->get()
            ->map(fn ($r) => ['method' => $r->method, 'count' => (int) $r->cnt, 'amount' => (float) $r->amt])->all();

        $channels = (clone $base)->where('payment_method', 'online')
            ->selectRaw('payment_channel as channel, count(*) as cnt')
            ->groupBy('payment_channel')->orderByDesc('cnt')->get()
            ->map(fn ($r) => ['channel' => $r->channel ?: 'Other', 'count' => (int) $r->cnt])->all();

        return ['methods' => $methods, 'channels' => $channels];
    }

    public function formsUtilization(): array
    {
        $rows = FormStock::with('batches')->get()->map(function (FormStock $s) {
            $registered = $s->batches->sum(fn ($b) => $b->startingQty());
            $used = $s->batches->sum(fn ($b) => $b->usedQty());
            $void = TransactionLog::where('form_type', $s->form_code)->where('status', 'Cancelled')->count();
            return [
                'name' => $s->form_name, 'code' => $s->form_code,
                'registered' => (int) $registered, 'used' => (int) $used, 'void' => (int) $void,
                'remaining' => (int) max(0, $registered - $used),
            ];
        })->values();

        return [
            'rows' => $rows->all(),
            'totalRegistered' => (int) $rows->sum('registered'),
            'usedThisPeriod' => (int) $rows->sum('used'),
            'lowStock' => (int) $rows->where('remaining', '<', 50)->count(),
        ];
    }

    public function recentActivity(int $limit = 8): array
    {
        $logs = TransactionLog::latest()->limit($limit)->get()
            ->map(fn ($l) => ['label' => $l->serial_number . ' · ' . $l->payee, 'module' => 'Collections', 'amount' => (float) $l->amount, 'at' => (string) $l->created_at]);

        $cheques = Cheque::latest()->limit($limit)->get()
            ->map(fn ($c) => ['label' => 'Cheque #' . $c->check_number . ' issued', 'module' => 'Cheque Mgmt', 'amount' => (float) $c->amount, 'at' => (string) $c->created_at]);

        $deposits = Deposit::latest()->limit($limit)->get()
            ->map(fn ($d) => ['label' => 'Deposit slip #' . ($d->slip_number ?: $d->id) . ' recorded', 'module' => 'Bank Recon', 'amount' => null, 'at' => (string) $d->created_at]);

        return $logs->concat($cheques)->concat($deposits)
            ->sortByDesc('at')->take($limit)->values()->all();
    }
}
