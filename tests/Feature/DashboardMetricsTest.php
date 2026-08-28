<?php
use App\Models\Cheque;
use App\Models\BankAccount;
use App\Models\FormStock;
use App\Models\BurialPermitTransaction;
use App\Models\TransactionLog;
use App\Services\DashboardMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedCollection(string $method, float $amount, string $status = 'Completed', ?string $when = null): TransactionLog
{
    $stock = FormStock::firstOrCreate(['form_code' => 'Form 58'], ['qty' => 50, 'form_name' => 'Burial Permit', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $txn = $stock->burialPermitTransactions()->create(['certificate_number' => (string) random_int(1, 99999), 'deceased_name' => 'X', 'fee_amount' => $amount]);

    return TransactionLog::create([
        'serial_number' => 'No. ' . random_int(1000, 9999), 'payee' => 'P', 'transacted_at' => $when ?? now(),
        'form_type' => 'Form 58', 'status' => $status, 'transaction_id' => $txn->id,
        'transaction_type' => BurialPermitTransaction::class, 'amount' => $amount,
        'payment_method' => $method, 'recon_status' => 'pending',
    ]);
}

it('sums live collections in the period and ignores cancelled', function () {
    seedCollection('cash', 250);
    seedCollection('online', 500);
    seedCollection('cash', 999, 'Cancelled');           // excluded
    seedCollection('cash', 100, 'Completed', now()->subMonths(2)->toDateString()); // out of range

    $m = new DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());

    expect($m->collections()['total'])->toEqual(750.0);
    expect($m->collections()['count'])->toBe(2);
});

it('sums issued cheques as disbursed and ignores cancelled', function () {
    $acc = BankAccount::create(['bank_name' => 'LBP', 'account_number' => '01', 'account_name' => 'M', 'is_active' => true]);
    Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => now(), 'check_number' => '1', 'pay_to_order_of' => 'A', 'amount' => 1000, 'amount_in_words' => 'x', 'status' => 'Issued', 'recon_status' => 'pending']);
    Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => now(), 'check_number' => '2', 'pay_to_order_of' => 'B', 'amount' => 500, 'amount_in_words' => 'x', 'status' => 'Cancelled', 'recon_status' => 'pending']);

    $m = new DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());

    expect($m->disbursed()['total'])->toEqual(1000.0);
    expect($m->disbursed()['count'])->toBe(1);
});
