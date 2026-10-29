<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Cheque;
use App\Models\Deposit;
use App\Models\FormBatch;
use App\Models\TransactionLog;
use App\Services\DashboardMetrics;
use Illuminate\Support\Carbon;

/**
 * Fills the current month (1st through today) with presentation data: a full
 * set of collections on every form, this month's cheques, daily deposits, and
 * a mix of matched / pending / bounced reconciliation so the Dashboard looks
 * alive. Also lifts the LBP opening balance if Cash Position would be low.
 *
 * Run once: php artisan db:seed --class=DemoMonthSeeder
 */
class DemoMonthSeeder extends DummyReportPreviewSeeder
{
    private const CASH_TARGET = 4250000.00;

    private int $monthsBack = 0;

    private int $failedLogs = 0;

    public function run(): void
    {
        // Last month first, so this month's growth figure on the Dashboard is sensible.
        foreach ([1, 0] as $monthsBack) {
            $this->monthsBack = $monthsBack;
            $this->failedLogs = 0;
            $startedAt = now()->subSecond();

            parent::run();

            FormBatch::whereNotNull('assigned_to')->whereNull('received_at')
                ->where('created_at', '>=', $startedAt)->update(['received_at' => now()]);

            $this->reconcileCollections($startedAt);
        }

        if (Cheque::whereBetween('cheque_date', [now()->startOfMonth(), now()])->count() < 20) {
            (new ChequeDummySeeder)->run();
        }

        $this->reconcileCheques();
        $this->liftCashPosition();

        $this->command?->info('Demo data for '.now()->format('F Y').' is ready.');
    }

    /** Spread dates from the 1st of this month up to now, never in the future. */
    protected function spreadDate(int $i, int $n): Carbon
    {
        $month = now()->startOfMonth()->subMonthsNoOverflow($this->monthsBack);
        $lastDay = $this->monthsBack ? $month->daysInMonth - 1 : now()->day - 1;
        $day = (int) round($i / max(1, $n - 1) * max(0, $lastDay));
        $date = $month->copy()->addDays($day)->setTime(8 + ($i % 8), ($i * 7) % 60);

        return $date->isFuture() ? now()->subMinutes($i + 1) : $date;
    }

    private function reconcileCollections(Carbon $startedAt): void
    {
        $account = BankAccount::orderBy('id')->first();
        if (! $account) {
            return;
        }

        $logs = TransactionLog::where('created_at', '>=', $startedAt)
            ->where('status', 'Completed')->orderBy('transacted_at')->get();

        foreach ($logs->groupBy(fn ($log) => $log->transacted_at->toDateString()) as $day => $dayLogs) {
            $deposit = Deposit::create([
                'deposit_date' => $day,
                'bank_account_id' => $account->id,
                'slip_number' => 'DEMO-'.str_replace('-', '', $day),
                'prepared_by' => 'Cleofe Villanueva',
            ]);

            foreach ($dayLogs->values() as $i => $log) {
                $log->update([
                    'deposit_id' => $deposit->id,
                    'recon_status' => $this->reconStatusFor($log, $i),
                ]);
            }
        }
    }

    /** Mostly matched, some still pending, and a few bounced cheques. */
    private function reconStatusFor(TransactionLog $log, int $i): string
    {
        if ($this->monthsBack === 0 && $this->failedLogs < 3 && $log->payment_method === 'cheque' && $i % 5 === 0) {
            $this->failedLogs++;

            return 'failed';
        }

        return $i % 4 === 0 ? 'pending' : 'completed';
    }

    private function reconcileCheques(): void
    {
        $cheques = Cheque::where('status', 'Issued')
            ->whereBetween('cheque_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->orderBy('cheque_date')->get();

        foreach ($cheques as $i => $cheque) {
            $cheque->update(['recon_status' => match (true) {
                $i === 1, $i === 7 => 'failed',
                $i % 3 === 0 => 'pending',
                default => 'completed',
            }]);
        }
    }

    private function liftCashPosition(): void
    {
        $account = BankAccount::orderBy('id')->first();
        if (! $account) {
            return;
        }

        $current = DashboardMetrics::forRange('month')->cashPosition()['total'];

        if ($current < self::CASH_TARGET) {
            $account->update(['opening_balance' => (float) $account->opening_balance + self::CASH_TARGET - $current]);
        }
    }
}
