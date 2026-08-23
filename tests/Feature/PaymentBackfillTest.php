<?php

use App\Models\BurialPermitTransaction;
use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Support\PaymentBackfill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('backfills amount from the linked transaction and defaults method to cash', function () {
    $stock = FormStock::create(['qty' => 5, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $txn = $stock->burialPermitTransactions()->create(['certificate_number' => '1', 'deceased_name' => 'X', 'fee_amount' => 250]);

    $log = TransactionLog::create([
        'serial_number' => 'No. 1', 'payee' => 'X', 'transacted_at' => now(),
        'form_type' => 'Form 58', 'status' => 'Completed',
        'transaction_id' => $txn->id, 'transaction_type' => BurialPermitTransaction::class,
    ]); // no amount passed

    expect($log->fresh()->amount)->toBeNull();

    PaymentBackfill::run();

    $log->refresh();
    expect((float) $log->amount)->toBe(250.0);
    expect($log->payment_method)->toBe('cash');
    expect($log->recon_status)->toBe('pending');
});
