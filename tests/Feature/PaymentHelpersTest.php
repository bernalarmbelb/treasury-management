<?php

use Illuminate\Http\Request;

it('maps a cheque payment to log fields and nulls the rest', function () {
    $req = Request::create('/', 'POST', [
        'payment_method' => 'cheque',
        'payer_bank_name' => 'LBP',
        'payment_reference' => '626890',
        'payment_reference_date' => '2026-08-20',
        'payment_channel' => 'GCash', // should be ignored for cheque
    ]);
    $fields = collection_payment_log_fields($req, 500);
    expect($fields['payment_method'])->toBe('cheque');
    expect($fields['payer_bank_name'])->toBe('LBP');
    expect($fields['payment_reference'])->toBe('626890');
    expect($fields['payment_channel'])->toBeNull();
    expect($fields['amount'])->toBe(500);
    expect($fields['recon_status'])->toBe('pending');
});

it('nulls all detail fields for cash', function () {
    $req = Request::create('/', 'POST', ['payment_method' => 'cash']);
    $fields = collection_payment_log_fields($req, 0);
    expect($fields['payment_channel'])->toBeNull();
    expect($fields['payer_bank_name'])->toBeNull();
    expect($fields['payment_reference'])->toBeNull();
    expect($fields['payment_reference_date'])->toBeNull();
});

it('exposes conditional validation rules', function () {
    $rules = collection_payment_rules();
    expect($rules['payment_method'])->toContain('required');
    expect($rules['payer_bank_name'])->toContain('required_if:payment_method,cheque');
    expect($rules['payment_channel'])->toContain('required_if:payment_method,online');
});
