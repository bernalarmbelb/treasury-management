<?php

use App\Models\BurialPermitTransaction;
use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows payment method + details on the view page', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 5, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $txn = $stock->burialPermitTransactions()->create(['certificate_number' => '1', 'deceased_name' => 'X', 'fee_amount' => 250]);
    $log = TransactionLog::create([
        'serial_number' => 'No. 1', 'payee' => 'X', 'transacted_at' => now(), 'form_type' => 'Form 58',
        'status' => 'Completed', 'transaction_id' => $txn->id, 'transaction_type' => BurialPermitTransaction::class,
        'amount' => 250, 'payment_method' => 'cheque', 'payer_bank_name' => 'LBP',
        'payment_reference' => '626890', 'payment_reference_date' => '2026-08-20', 'recon_status' => 'pending',
    ]);

    $this->actingAs($user)->get("/collections/{$log->id}")
        ->assertOk()
        ->assertSee('Cheque')
        ->assertSee('LBP')
        ->assertSee('626890');
});
