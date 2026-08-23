<?php

use App\Models\BankAccount;
use App\Models\Cheque;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $this->account = BankAccount::create([
        'bank_name' => 'LBP — Sorsogon Branch',
        'account_number' => '00782-1019-43',
        'account_name' => 'Municipality of Prieto Diaz',
        'is_active' => true,
    ]);
});

function validChequePayload(int $accountId, array $overrides = []): array
{
    return array_merge([
        'bank_account_id' => $accountId,
        'cheque_date' => '2026-08-23',
        'check_number' => '626888',
        'pay_to_order_of' => 'JRE Agrivet Supply',
        'amount' => 6090,
        'nature_of_payment' => 'purchase',
    ], $overrides);
}

it('renders the cheque logs page', function () {
    $this->actingAs($this->user)
        ->get('/cheque-management')
        ->assertOk()
        ->assertSee('Cheque Management');
});

it('renders the create cheque form with the bank account option', function () {
    $this->actingAs($this->user)
        ->get('/cheque-management/create')
        ->assertOk()
        ->assertSee('00782-1019-43');
});

it('stores a cheque and auto-generates the amount in words', function () {
    $this->actingAs($this->user)
        ->postJson('/cheque-management', validChequePayload($this->account->id))
        ->assertOk()
        ->assertJsonPath('message', 'Cheque saved successfully.');

    $cheque = Cheque::first();
    expect($cheque)->not->toBeNull();
    expect($cheque->amount_in_words)->toBe('Six thousand ninety and 00/100 pesos');
    expect($cheque->account_name)->toBe('Municipality of Prieto Diaz');
    expect($cheque->status)->toBe('Issued');
});

it('blocks a reused cheque number on the same bank account', function () {
    Cheque::create([
        'bank_account_id' => $this->account->id,
        'account_name' => $this->account->account_name,
        'cheque_date' => '2026-08-20',
        'check_number' => '626888',
        'pay_to_order_of' => 'Existing Payee',
        'amount' => 100,
        'amount_in_words' => Cheque::spellAmount(100),
        'status' => 'Issued',
    ]);

    $this->actingAs($this->user)
        ->postJson('/cheque-management', validChequePayload($this->account->id))
        ->assertStatus(422)
        ->assertJsonPath('message', fn ($m) => str_contains($m, 'already been used'));

    expect(Cheque::where('check_number', '626888')->count())->toBe(1);
});

it('allows the same cheque number on a different bank account', function () {
    $other = BankAccount::create([
        'bank_name' => 'DBP — Sorsogon Branch',
        'account_number' => '1462-1005-88',
        'account_name' => 'Municipality of Prieto Diaz',
        'is_active' => true,
    ]);

    Cheque::create([
        'bank_account_id' => $this->account->id,
        'account_name' => $this->account->account_name,
        'cheque_date' => '2026-08-20',
        'check_number' => '626888',
        'pay_to_order_of' => 'Existing Payee',
        'amount' => 100,
        'amount_in_words' => Cheque::spellAmount(100),
        'status' => 'Issued',
    ]);

    $this->actingAs($this->user)
        ->postJson('/cheque-management', validChequePayload($other->id))
        ->assertOk();

    expect(Cheque::where('check_number', '626888')->count())->toBe(2);
});

it('renders the view, print, and duplicate pages for a cheque', function () {
    $cheque = Cheque::create([
        'bank_account_id' => $this->account->id,
        'account_name' => $this->account->account_name,
        'cheque_date' => '2026-08-20',
        'check_number' => '626890',
        'pay_to_order_of' => 'Sirit Const. & Supply',
        'amount' => 31787.70,
        'amount_in_words' => Cheque::spellAmount(31787.70),
        'status' => 'Issued',
    ]);

    $this->actingAs($this->user)->get("/cheque-management/{$cheque->id}")->assertOk()->assertSee('Sirit Const. &amp; Supply', false);
    $this->actingAs($this->user)->get("/cheque-management/{$cheque->id}/print")->assertOk()->assertSee('Pay to the order of');
    $this->actingAs($this->user)->get("/cheque-management/{$cheque->id}/duplicate")->assertOk()->assertSee('Duplicate Copy');
});

it('cancels then archives a cheque', function () {
    $cheque = Cheque::create([
        'bank_account_id' => $this->account->id,
        'account_name' => $this->account->account_name,
        'cheque_date' => '2026-08-20',
        'check_number' => '626891',
        'pay_to_order_of' => 'Void Payee',
        'amount' => 500,
        'amount_in_words' => Cheque::spellAmount(500),
        'status' => 'Issued',
    ]);

    // Cannot archive while still Issued.
    $this->actingAs($this->user)->postJson("/cheque-management/{$cheque->id}/archive")->assertStatus(422);

    $this->actingAs($this->user)->postJson("/cheque-management/{$cheque->id}/cancel")->assertOk();
    expect($cheque->fresh()->status)->toBe('Cancelled');

    $this->actingAs($this->user)->postJson("/cheque-management/{$cheque->id}/archive")->assertOk();
    expect($cheque->fresh()->archived_at)->not->toBeNull();
});

it('adds a bank account', function () {
    $this->actingAs($this->user)
        ->postJson('/cheque-management/bank-accounts', [
            'bank_name' => 'DBP — Sorsogon Branch',
            'account_number' => '1462-1005-88',
            'account_name' => 'Municipality of Prieto Diaz',
        ])
        ->assertOk()
        ->assertJsonPath('label', 'DBP — Sorsogon Branch · 1462-1005-88');

    expect(BankAccount::where('account_number', '1462-1005-88')->exists())->toBeTrue();
});

it('rejects a duplicate bank account number', function () {
    $this->actingAs($this->user)
        ->postJson('/cheque-management/bank-accounts', [
            'bank_name' => 'LBP Copy',
            'account_number' => '00782-1019-43', // already created in beforeEach
            'account_name' => 'Municipality of Prieto Diaz',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('account_number');

    expect(BankAccount::where('account_number', '00782-1019-43')->count())->toBe(1);
});

it('defaults the report to the latest cheque account and period', function () {
    // A second account sorts first alphabetically (DBP < LBP) but has no cheques.
    BankAccount::create(['bank_name' => 'DBP — Sorsogon Branch', 'account_number' => '1462-1005-88', 'account_name' => 'Municipality of Prieto Diaz', 'is_active' => true]);

    // The only cheque lives on the LBP account (from beforeEach), dated Dec 2013.
    Cheque::create([
        'bank_account_id' => $this->account->id,
        'account_name' => $this->account->account_name,
        'cheque_date' => '2013-12-27',
        'check_number' => '626886',
        'pay_to_order_of' => 'CEE / Municipal Treasurer',
        'amount' => 6090.00,
        'amount_in_words' => Cheque::spellAmount(6090),
        'nature_of_payment' => 'withdrawal',
        'status' => 'Issued',
    ]);

    // No query params — should open on LBP · December 2013, not DBP · current month.
    $this->actingAs($this->user)
        ->get('/cheque-management/report')
        ->assertOk()
        ->assertSee('00782-1019-43')       // LBP account (the one with data)
        ->assertSee('December 2013')       // latest cheque period
        ->assertSee('6,090.00')            // the cheque shows
        ->assertDontSee('No cheques issued for this period.');
});

it('renders the report of checks issued', function () {
    Cheque::create([
        'bank_account_id' => $this->account->id,
        'account_name' => $this->account->account_name,
        'cheque_date' => '2026-08-15',
        'check_number' => '626892',
        'pay_to_order_of' => 'JRE Agrivet Supply',
        'amount' => 19772.79,
        'amount_in_words' => Cheque::spellAmount(19772.79),
        'nature_of_payment' => 'purchase',
        'status' => 'Issued',
    ]);

    $this->actingAs($this->user)
        ->get('/cheque-management/report?bank_account_id=' . $this->account->id . '&month=8&year=2026')
        ->assertOk()
        ->assertSee('REPORTS OF CHECKS ISSUED')
        ->assertSee('19,772.79')
        ->assertSee('Gemma D. Ferrer', false)
        ->assertSee('Municipal Treasurer');
});
