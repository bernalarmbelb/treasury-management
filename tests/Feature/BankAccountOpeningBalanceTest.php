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

it('accepts opening balance when adding a bank account', function () {
    $role = App\Models\Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = App\Models\User::factory()->create(['status' => App\Models\User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)
        ->post('/cheque-management/bank-accounts', [
            'bank_name' => 'LBP Test', 'account_number' => 'OB-999', 'account_name' => 'MTO', 'opening_balance' => 50000,
        ])
        ->assertOk();

    expect(App\Models\BankAccount::where('account_number', 'OB-999')->first()->opening_balance)->toEqual('50000.00');
});

it('defaults opening balance to zero when omitted at the route', function () {
    $role = App\Models\Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = App\Models\User::factory()->create(['status' => App\Models\User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)->post('/cheque-management/bank-accounts', [
        'bank_name' => 'DBP Test', 'account_number' => 'OB-000', 'account_name' => 'MTO',
    ])->assertOk();

    expect((float) App\Models\BankAccount::where('account_number', 'OB-000')->first()->opening_balance)->toBe(0.0);
});

it('treats a blank opening balance from the form the same as omitted', function () {
    $role = App\Models\Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = App\Models\User::factory()->create(['status' => App\Models\User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)->post('/cheque-management/bank-accounts', [
        'bank_name' => 'PVB Test', 'account_number' => 'OB-111', 'account_name' => 'MTO', 'opening_balance' => '',
    ])->assertOk();

    expect((float) App\Models\BankAccount::where('account_number', 'OB-111')->first()->opening_balance)->toBe(0.0);
});
