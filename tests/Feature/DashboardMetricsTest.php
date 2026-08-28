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

it('computes cash position as opening + deposits in - cheques out', function () {
    $acc = App\Models\BankAccount::create(['bank_name' => 'LBP', 'account_number' => '9', 'account_name' => 'M', 'is_active' => true, 'opening_balance' => 100000]);
    $dep = App\Models\Deposit::create(['deposit_date' => now(), 'bank_account_id' => $acc->id, 'slip_number' => 'S1', 'prepared_by' => 'T']);
    $c = seedCollection('cash', 5000);
    $c->update(['deposit_id' => $dep->id]);
    App\Models\Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => now(), 'check_number' => 'X1', 'pay_to_order_of' => 'A', 'amount' => 20000, 'amount_in_words' => 'x', 'status' => 'Issued', 'recon_status' => 'pending']);

    $m = new App\Services\DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());

    // 100000 + 5000 - 20000
    expect($m->cashPosition()['total'])->toEqual(85000.0);
    expect($m->cashPosition()['accounts'])->toBe(1);
});

it('counts failed cheques and payments as exceptions', function () {
    $acc = App\Models\BankAccount::create(['bank_name' => 'LBP', 'account_number' => '7', 'account_name' => 'M', 'is_active' => true]);
    App\Models\Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => now(), 'check_number' => 'B1', 'pay_to_order_of' => 'A', 'amount' => 12000, 'amount_in_words' => 'x', 'status' => 'Issued', 'recon_status' => 'failed']);
    $bad = seedCollection('online', 1200); $bad->update(['recon_status' => 'failed']);
    seedCollection('cash', 300); // fine

    $m = new App\Services\DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());

    expect($m->exceptions()['count'])->toBe(2);
    expect($m->exceptions()['items'])->toHaveCount(2);
});

it('computes reconciliation matched percentages', function () {
    $ok = seedCollection('cash', 100); $ok->update(['recon_status' => 'completed']);
    seedCollection('cash', 100); // pending
    $acc = App\Models\BankAccount::create(['bank_name' => 'LBP', 'account_number' => '5', 'account_name' => 'M', 'is_active' => true]);
    App\Models\Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => now(), 'check_number' => 'C1', 'pay_to_order_of' => 'A', 'amount' => 1, 'amount_in_words' => 'x', 'status' => 'Issued', 'recon_status' => 'completed']);

    $m = new App\Services\DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());

    expect($m->reconciliation()['depositsMatchedPct'])->toBe(50);
    expect($m->reconciliation()['chequesMatchedPct'])->toBe(100);
});

it('orders exceptions items newest-first across cheques and logs', function () {
    $acc = App\Models\BankAccount::create(['bank_name' => 'LBP', 'account_number' => '8', 'account_name' => 'M', 'is_active' => true]);
    $cheque = App\Models\Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => now(), 'check_number' => 'D1', 'pay_to_order_of' => 'A', 'amount' => 500, 'amount_in_words' => 'x', 'status' => 'Issued', 'recon_status' => 'failed']);
    $cheque->forceFill(['updated_at' => now()->subDay()])->save();

    $log = seedCollection('online', 700);
    $log->update(['recon_status' => 'failed']);
    $log->forceFill(['updated_at' => now()])->save();

    $m = new App\Services\DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());

    expect($m->exceptions()['items'][0]['type'])->toBe('failed-payment');
});

it('builds a daily collections trend with summary', function () {
    seedCollection('cash', 100, 'Completed', now()->startOfMonth()->toDateString());
    seedCollection('cash', 300, 'Completed', now()->startOfMonth()->toDateString());   // same day => 400
    seedCollection('cash', 900, 'Completed', now()->startOfMonth()->addDay()->toDateString());

    $m = new App\Services\DashboardMetrics(now()->startOfMonth(), now()->endOfMonth());
    $t = $m->trend();

    expect($t['points'])->toHaveCount(2);
    expect($t['total'])->toEqual(1300.0);
    expect($t['peak']['amount'])->toEqual(900.0);
});
