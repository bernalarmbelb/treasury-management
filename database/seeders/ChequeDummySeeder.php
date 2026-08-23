<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Cheque;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Adds 50 dummy cheques on the LBP account for the current month, so the
 * "Report of Checks Issued" can be seen fully populated. Idempotent per
 * check number. Run: php artisan db:seed --class=ChequeDummySeeder
 */
class ChequeDummySeeder extends Seeder
{
    public function run(): void
    {
        $lbp = BankAccount::where('account_number', '00782-1019-43')->first();

        if (! $lbp) {
            $this->command?->warn('LBP account not found — run ChequeManagementSeeder first.');

            return;
        }

        $payees = [
            'JRE Agrivet Supply', 'B. Esperida Trading', 'Sirit Const. & Supply', 'Electroworld Inc.',
            'Sorsogon Electric Coop II', 'Prieto Diaz Waterworks', 'Grand Imperial Hardware',
            'Petron - Prieto Diaz', 'MDF Office Supplies', 'Bicol Medical Supplies',
            'Sorsogon Printing Press', 'Ace Hardware Sorsogon', 'Rural Health Unit Supplies',
            'Ferrer Catering Services', 'CEE / Municipal Treasurer', 'DPWH Materials Supply',
            'PhilHealth Remittance', 'GSIS Remittance', 'BIR - Withholding Tax', 'LGU Payroll - Casual',
        ];
        $natures = ['purchase', 'withdrawal', 'salary', 'supplies', 'services', 'fuel', 'remittance'];

        // Continue the check-number series after the current max on this account.
        $maxNumber = (int) (Cheque::where('bank_account_id', $lbp->id)->max('check_number') ?: 626886);

        // Deterministic so re-running produces the same set.
        mt_srand(2026);

        $count = 50;
        $cancelledEvery = 13; // ~4 cancelled out of 50

        for ($i = 1; $i <= $count; $i++) {
            $number = (string) ($maxNumber + $i);
            $day = 1 + (($i * 3) % 28);              // spread across the month
            $date = Carbon::create(now()->year, now()->month, $day, 9, ($i * 7) % 60);
            $status = ($i % $cancelledEvery === 0) ? 'Cancelled' : 'Issued';
            $amount = round(mt_rand(150000, 9800000) / 100, 2); // 1,500.00 – 98,000.00
            $payee = $payees[($i - 1) % count($payees)];
            $nature = $natures[($i - 1) % count($natures)];

            Cheque::firstOrCreate(
                ['bank_account_id' => $lbp->id, 'check_number' => $number],
                [
                    'account_name' => $lbp->account_name,
                    'cheque_date' => $date,
                    'pay_to_order_of' => $payee,
                    'amount' => $amount,
                    'amount_in_words' => Cheque::spellAmount($amount),
                    'nature_of_payment' => $nature,
                    'status' => $status,
                    'created_by' => 'Seeder',
                    'created_at' => $date,
                    'updated_at' => $date,
                ],
            );
        }

        mt_srand();

        $this->command?->info("Added up to {$count} dummy cheques on {$lbp->bank_name} for " . now()->format('F Y') . '.');
    }
}
