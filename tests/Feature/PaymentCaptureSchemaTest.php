<?php

use App\Models\MarriageCertificateTransaction;
use App\Models\TransactionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('adds payment columns to transaction_logs', function () {
    foreach (['amount', 'payment_method', 'payment_channel', 'payer_bank_name', 'payment_reference', 'payment_reference_date', 'recon_status'] as $col) {
        expect(Schema::hasColumn('transaction_logs', $col))->toBeTrue();
    }
});

it('adds fee_amount to marriage and exposes new fillables', function () {
    expect(Schema::hasColumn('marriage_certificate_transactions', 'fee_amount'))->toBeTrue();
    expect((new TransactionLog)->getFillable())->toContain('payment_method', 'amount', 'recon_status');
    expect((new MarriageCertificateTransaction)->getFillable())->toContain('fee_amount');
});
