<?php

use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('maps an Official Receipt cheque payment onto the log (check -> cheque)', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 10, 'form_name' => 'Official Receipt', 'form_code' => 'Form 5IC', 'added_date' => now()->toDateString(), 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/official-receipt", [
        'payor' => 'Juan Dela Cruz',
        'items' => [['description' => 'Clearance', 'account_code' => '', 'amount' => 100]],
        'total' => 100,
        'payment_method' => 'check',
        'drawee_bank' => 'LBP',
        'check_number' => 'CHK-01',
        'check_date' => '2026-08-20',
    ])->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('cheque');            // normalized from 'check'
    expect($log->payer_bank_name)->toBe('LBP');
    expect($log->payment_reference)->toBe('CHK-01');
    expect((float) $log->amount)->toBe(100.0);
    expect($log->recon_status)->toBe('pending');
});

it('captures cash on an Official Receipt log', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 10, 'form_name' => 'Official Receipt', 'form_code' => 'Form 5IC', 'added_date' => now()->toDateString(), 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/official-receipt", [
        'payor' => 'Maria Santos',
        'items' => [['description' => 'Fee', 'account_code' => '', 'amount' => 75]],
        'total' => 75,
        'payment_method' => 'cash',
    ])->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('cash');
    expect($log->payer_bank_name)->toBeNull();
    expect($log->payment_reference)->toBeNull();
    expect((float) $log->amount)->toBe(75.0);
});
