<?php

use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('captures cheque payment on a burial transaction log', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 5, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $stock->batches()->create(['registration_date' => now(), 'purchase_date' => now(), 'starting_serial_number' => '0000001', 'ending_serial_number' => '0000010', 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/burial", [
        'certificate_number' => '0000001',
        'series_letter' => 'C',
        'applicant_name' => 'Applicant Name',
        'deceased_name' => 'Juan Dela Cruz',
        'fee_amount' => 300,
        'payment_method' => 'cheque',
        'payer_bank_name' => 'LBP',
        'payment_reference' => '626890',
        'payment_reference_date' => '2026-08-20',
    ])->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('cheque');
    expect($log->payer_bank_name)->toBe('LBP');
    expect($log->payment_reference)->toBe('626890');
    expect((float) $log->amount)->toBe(300.0);
    expect($log->recon_status)->toBe('pending');
});

it('captures cash payment with null detail fields', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 5, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $stock->batches()->create(['registration_date' => now(), 'purchase_date' => now(), 'starting_serial_number' => '0000001', 'ending_serial_number' => '0000010', 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/burial", [
        'certificate_number' => '0000002',
        'series_letter' => 'C',
        'applicant_name' => 'Applicant Name',
        'deceased_name' => 'Maria Santos',
        'fee_amount' => 150,
        'payment_method' => 'cash',
    ])->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('cash');
    expect($log->payer_bank_name)->toBeNull();
    expect($log->payment_reference)->toBeNull();
    expect((float) $log->amount)->toBe(150.0);
});
