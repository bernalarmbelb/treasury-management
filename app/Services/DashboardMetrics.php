<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Cheque;
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
            ->map(fn ($c) => ['type' => 'bounced-cheque', 'label' => 'Cheque #' . $c->check_number . ' · ' . $c->pay_to_order_of, 'amount' => (float) $c->amount]);

        $logs = TransactionLog::where('recon_status', 'failed')
            ->orderByDesc('updated_at')->limit(6)->get()
            ->map(fn ($l) => ['type' => 'failed-payment', 'label' => $l->serial_number . ' · ' . $l->payee, 'amount' => (float) $l->amount]);

        $items = $cheques->concat($logs)->take(6)->values()->all();
        $count = Cheque::where('recon_status', 'failed')->count() + TransactionLog::where('recon_status', 'failed')->count();

        return ['count' => $count, 'items' => $items];
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
}
