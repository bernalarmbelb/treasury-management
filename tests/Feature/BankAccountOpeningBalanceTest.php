<?php
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores an opening balance on a bank account', function () {
    $acc = BankAccount::create([
        'bank_name' => 'LBP', 'account_number' => '001', 'account_name' => 'MTO',
        'is_active' => true, 'opening_balance' => 150000.50,
    ]);

    expect($acc->fresh()->opening_balance)->toEqual('150000.50');
});

it('defaults opening balance to zero', function () {
    $acc = BankAccount::create([
        'bank_name' => 'DBP', 'account_number' => '002', 'account_name' => 'MTO', 'is_active' => true,
    ]);

    expect((float) $acc->fresh()->opening_balance)->toBe(0.0);
});
