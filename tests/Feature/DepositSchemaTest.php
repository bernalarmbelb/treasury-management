<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('adds the deposits table and reconciliation columns', function () {
    expect(Schema::hasTable('deposits'))->toBeTrue();
    foreach (['deposit_date', 'bank_account_id', 'slip_number', 'prepared_by'] as $col) {
        expect(Schema::hasColumn('deposits', $col))->toBeTrue();
    }
    expect(Schema::hasColumn('transaction_logs', 'deposit_id'))->toBeTrue();
    expect(Schema::hasColumn('cheques', 'recon_status'))->toBeTrue();
});
