<?php

use App\Models\BankAccount;
use App\Models\BurialPermitTransaction;
use App\Models\Cheque;
use App\Models\Deposit;
use App\Models\FormStock;
use App\Models\Role;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAdmin(): User
{
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $u = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $u->roles()->sync([$role->id]);

    return $u;
}

function makeCollection(string $method, float $amount): TransactionLog
{
    $stock = FormStock::firstOrCreate(['form_code' => 'Form 58'], ['qty' => 50, 'form_name' => 'Burial', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $txn = $stock->burialPermitTransactions()->create(['certificate_number' => (string) random_int(1, 99999), 'deceased_name' => 'X', 'fee_amount' => $amount]);

    return TransactionLog::create([
        'serial_number' => 'No. ' . random_int(1000, 9999), 'payee' => 'Payee', 'transacted_at' => now(),
        'form_type' => 'Form 58', 'status' => 'Completed', 'transaction_id' => $txn->id,
        'transaction_type' => BurialPermitTransaction::class, 'amount' => $amount, 'payment_method' => $method, 'recon_status' => 'pending',
    ]);
}

it('records a deposit and completes the collections', function () {
    $admin = makeAdmin();
    $acc = BankAccount::create(['bank_name' => 'LBP', 'account_number' => '00782-1019-43', 'account_name' => 'M', 'is_active' => true]);
    $c1 = makeCollection('cash', 250);
    $c2 = makeCollection('cheque', 6090);

    $this->actingAs($admin)->postJson('/bank-deposit-reconciliation/deposits', [
        'ids' => [$c1->id, $c2->id], 'bank_account_id' => $acc->id, 'deposit_date' => '2026-08-20', 'slip_number' => 'DS-001',
    ])->assertOk();

    expect(Deposit::count())->toBe(1);
    expect($c1->fresh()->recon_status)->toBe('completed');
    expect($c2->fresh()->recon_status)->toBe('completed');
    expect($c1->fresh()->deposit_id)->not->toBeNull();
    expect((float) Deposit::first()->total())->toBe(6340.0);
});

it('will not deposit an online payment', function () {
    $admin = makeAdmin();
    $acc = BankAccount::create(['bank_name' => 'LBP', 'account_number' => '1', 'account_name' => 'M', 'is_active' => true]);
    $online = makeCollection('online', 500);

    $this->actingAs($admin)->postJson('/bank-deposit-reconciliation/deposits', [
        'ids' => [$online->id], 'bank_account_id' => $acc->id, 'deposit_date' => '2026-08-20',
    ])->assertStatus(422);

    expect($online->fresh()->recon_status)->toBe('pending');
});

it('confirms an online payment', function () {
    $admin = makeAdmin();
    $online = makeCollection('online', 500);

    $this->actingAs($admin)->postJson("/bank-deposit-reconciliation/incoming/{$online->id}/confirm")->assertOk();
    expect($online->fresh()->recon_status)->toBe('completed');
});

it('marks an outgoing cheque cleared, then bounced', function () {
    $admin = makeAdmin();
    $acc = BankAccount::create(['bank_name' => 'LBP', 'account_number' => '1', 'account_name' => 'M', 'is_active' => true]);
    $cheque = Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'M', 'cheque_date' => '2026-08-10', 'check_number' => '626877', 'pay_to_order_of' => 'X', 'amount' => 100, 'amount_in_words' => 'x', 'status' => 'Issued']);

    $this->actingAs($admin)->postJson("/bank-deposit-reconciliation/cheques/{$cheque->id}/clear")->assertOk();
    expect($cheque->fresh()->recon_status)->toBe('completed');

    $this->actingAs($admin)->postJson("/bank-deposit-reconciliation/cheques/{$cheque->id}/bounce")->assertOk();
    expect($cheque->fresh()->recon_status)->toBe('failed');
});

it('blocks a non-admin from reconciling', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]); // no admin role
    $c1 = makeCollection('cash', 250);
    $acc = BankAccount::create(['bank_name' => 'LBP', 'account_number' => '1', 'account_name' => 'M', 'is_active' => true]);

    $this->actingAs($user)->postJson('/bank-deposit-reconciliation/deposits', [
        'ids' => [$c1->id], 'bank_account_id' => $acc->id, 'deposit_date' => '2026-08-20',
    ])->assertStatus(403);
});
