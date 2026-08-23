<?php

use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('captures a cash payment on a Corporation Cedula log', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 10, 'form_name' => 'Corporation Cedula', 'form_code' => 'BIR0017', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $stock->batches()->create(['registration_date' => now(), 'purchase_date' => now(), 'starting_serial_number' => '0000001', 'ending_serial_number' => '0000010', 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/corporation-cedula", [
        'certificate_prefix' => 'CCC',
        'certificate_number' => '0000005',
        'year' => 2026,
        'company_name' => 'Acme Corporation',
        'amount_paid' => 500,
        'payment_method' => 'cash',
    ])->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('cash');
    expect($log->payer_bank_name)->toBeNull();
    expect($log->payment_reference)->toBeNull();
    expect((float) $log->amount)->toBe(500.0);
});
