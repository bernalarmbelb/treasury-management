<?php

use App\Models\BankAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the reconciliation UI for admins', function () {
    $admin = makeAdmin();
    BankAccount::create(['bank_name' => 'LBP', 'account_number' => '00782-1019-43', 'account_name' => 'M', 'is_active' => true]);
    $cash = makeCollection('cash', 250);
    $online = makeCollection('online', 500);
    $cheque = makeCollection('cheque', 6090);

    $html = $this->actingAs($admin)->get('/bank-deposit-reconciliation/incoming')->assertOk()->getContent();

    // Depositable rows (cash/cheque) get a select checkbox + the select-all header.
    expect($html)->toContain('id="selectAllDepositable"');
    expect($html)->toContain('bdr-deposit-check" data-id="' . $cash->id);
    expect($html)->toContain('bdr-deposit-check" data-id="' . $cheque->id);
    // Online rows do NOT get a deposit checkbox but DO get a Confirm button.
    expect($html)->not->toContain('bdr-deposit-check" data-id="' . $online->id);
    expect($html)->toContain('bdr-confirm" data-id="' . $online->id);
    // Incoming cheque gets a Bounce button.
    expect($html)->toContain('bdr-bounce-in" data-id="' . $cheque->id);
    // Bulk bar + Record Deposit modal + bank account option are present.
    expect($html)->toContain('id="depBulkBar"');
    expect($html)->toContain('id="depOverlay"');
    expect($html)->toContain('00782-1019-43');
});

it('hides reconciliation controls from non-admins', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    makeCollection('cash', 250);

    $html = $this->actingAs($user)->get('/bank-deposit-reconciliation/incoming')->assertOk()->getContent();

    expect($html)->not->toContain('id="selectAllDepositable"');
    expect($html)->not->toContain('id="depBulkBar"');
    expect($html)->not->toContain('bdr-deposit-check');
});
