<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Cheque;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ChequeManagementSeeder extends Seeder
{
    public function run(): void
    {
        $lbp = BankAccount::firstOrCreate(
            ['account_number' => '00782-1019-43'],
            [
                'bank_name' => 'LBP — Sorsogon Branch',
                'account_name' => 'Municipality of Prieto Diaz',
                'is_active' => true,
            ],
        );

        BankAccount::firstOrCreate(
            ['account_number' => '1462-1005-88'],
            [
                'bank_name' => 'DBP — Sorsogon Branch',
                'account_name' => 'Municipality of Prieto Diaz',
                'is_active' => true,
            ],
        );

        // Sample cheques mirroring the "Report of Checks Issued" reference.
        $samples = [
            ['2013-12-09', '626877', 'JRE Agrivet Supply', 19772.79, 'purchase', 'Issued'],
            ['2013-12-10', '626878', 'B. Esperida Trading', 24523.86, 'purchase', 'Issued'],
            ['2013-12-11', '626880', null, 0, null, 'Cancelled'],
            ['2013-12-16', '626882', 'CEE / Municipal Treasurer', 22800.00, 'withdrawal', 'Issued'],
            ['2013-12-19', '626884', 'Sirit Const. & Supply', 31787.70, 'purchase', 'Issued'],
            ['2013-12-27', '626886', 'CEE / Municipal Treasurer', 6090.00, 'withdrawal', 'Issued'],
        ];

        foreach ($samples as [$date, $number, $payee, $amount, $nature, $status]) {
            Cheque::firstOrCreate(
                ['bank_account_id' => $lbp->id, 'check_number' => $number],
                [
                    'account_name' => $lbp->account_name,
                    'cheque_date' => Carbon::parse($date),
                    'pay_to_order_of' => $payee ?? '',
                    'amount' => $amount,
                    'amount_in_words' => $payee ? Cheque::spellAmount($amount) : '',
                    'nature_of_payment' => $nature,
                    'status' => $status,
                    'created_by' => 'Seeder',
                    'created_at' => Carbon::parse($date)->setTime(9, rand(0, 59)),
                ],
            );
        }
    }
}
