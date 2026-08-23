<?php

use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('captures a money-order payment on an Individual Cedula log', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 10, 'form_name' => 'Individual Cedula', 'form_code' => 'BIR0016', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $stock->batches()->create(['registration_date' => now(), 'purchase_date' => now(), 'starting_serial_number' => '0000001', 'ending_serial_number' => '0000010', 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/individual-cedula", [
        'certificate_prefix' => 'CCI',
        'certificate_number' => '0000005',
        'year' => 2026,
        'surname' => 'Cruz',
        'first_name' => 'Juan',
        'amount_paid' => 30,
        'payment_method' => 'money_order',
        'payment_reference' => 'MO-01',
        'payment_reference_date' => '2026-08-20',
    ])->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('money_order');
    expect($log->payment_reference)->toBe('MO-01');
    expect($log->payer_bank_name)->toBeNull();
    expect($log->payment_channel)->toBeNull();
    expect((float) $log->amount)->toBe(30.0);
});
