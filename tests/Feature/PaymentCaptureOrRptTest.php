<?php

use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('captures online payment on an OR/RPT transaction log', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 10, 'form_name' => 'OR RPT', 'form_code' => 'Form 56', 'added_date' => now()->toDateString(), 'added_by' => 'T']);

    $payload = [
        'client_name' => 'Juan Dela Cruz',
        'amount_paid' => 500,
        'entries' => [[
            'tax_declaration_number' => 'TD-001', 'declared_owner' => 'Juan', 'location' => 'Brgy',
            'lot_block_number' => 'Lot 1', 'assessed_value_land' => 60000, 'assessed_value_improvement' => 40000,
            'assessed_value_total' => 100000, 'tax_due' => 2000, 'payment_scheme' => 'installment',
            'installment_quarter' => 1, 'discount' => 0, 'penalty_percent' => 0, 'penalty_amount' => 0, 'amount' => 500,
        ]],
        'payment_method' => 'online',
        'payment_channel' => 'GCash',
        'payment_reference' => 'GC-123456',
        'payment_reference_date' => '2026-08-21',
    ];

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/or-rpt", $payload)->assertOk();

    $log = TransactionLog::first();
    expect($log->payment_method)->toBe('online');
    expect($log->payment_channel)->toBe('GCash');
    expect($log->payment_reference)->toBe('GC-123456');
    expect($log->payer_bank_name)->toBeNull();
    expect((float) $log->amount)->toBe(500.0);
});
