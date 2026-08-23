<?php

use App\Models\BankAccount;
use App\Models\BurialPermitTransaction;
use App\Models\Cheque;
use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);

    $stock = FormStock::create(['qty' => 5, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $txn = $stock->burialPermitTransactions()->create(['certificate_number' => '1', 'deceased_name' => 'X', 'fee_amount' => 250]);
    TransactionLog::create(['serial_number' => 'No. 1000', 'payee' => 'Juan Collector', 'transacted_at' => now(), 'form_type' => 'Form 58', 'status' => 'Completed', 'transaction_id' => $txn->id, 'transaction_type' => BurialPermitTransaction::class, 'amount' => 250, 'payment_method' => 'cash', 'recon_status' => 'pending']);

    $acc = BankAccount::create(['bank_name' => 'LBP', 'account_number' => '00782-1019-43', 'account_name' => 'Municipality', 'is_active' => true]);
    Cheque::create(['bank_account_id' => $acc->id, 'account_name' => 'Municipality', 'cheque_date' => '2026-08-10', 'check_number' => '626877', 'pay_to_order_of' => 'JRE Agrivet', 'amount' => 19772.79, 'amount_in_words' => 'x', 'status' => 'Issued']);
});

it('shows both incoming and outgoing in the combined ledger', function () {
    $this->actingAs($this->user)
        ->get('/bank-deposit-reconciliation')
        ->assertOk()
        ->assertSee('Juan Collector')      // incoming (collection)
        ->assertSee('JRE Agrivet')         // outgoing (cheque)
        ->assertSee('Incoming')
        ->assertSee('Outgoing');
});

it('shows only collections on the incoming tab', function () {
    $this->actingAs($this->user)
        ->get('/bank-deposit-reconciliation/incoming')
        ->assertOk()
        ->assertSee('Juan Collector')
        ->assertSee('Burial')              // form name
        ->assertDontSee('JRE Agrivet');    // cheques not here
});

it('shows only cheques on the outgoing tab', function () {
    $this->actingAs($this->user)
        ->get('/bank-deposit-reconciliation/outgoing')
        ->assertOk()
        ->assertSee('JRE Agrivet')
        ->assertSee('626877')
        ->assertDontSee('Juan Collector'); // collections not here
});
