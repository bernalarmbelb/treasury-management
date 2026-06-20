<?php

use App\Models\OrRptTransaction;
use App\Models\RptProperty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $this->formStock = makeOrRptFormStock();
});

function validOrRptPayload(array $overrides = []): array
{
    return array_merge([
        'client_name' => 'Juan Dela Cruz',
        'amount_paid' => 500,
        'entries' => [[
            'tax_declaration_number' => 'TD-001',
            'declared_owner' => 'Juan Dela Cruz',
            'location' => 'Brgy. Sablayan',
            'lot_block_number' => 'Lot 4 Blk 2',
            'assessed_value_land' => 60000,
            'assessed_value_improvement' => 40000,
            'assessed_value_total' => 100000,
            'tax_due' => 2000,
            'payment_scheme' => 'installment',
            'installment_quarter' => 1,
            'discount' => 0,
            'penalty_percent' => 0,
            'penalty_amount' => 0,
            'amount' => 500,
        ]],
    ], $overrides);
}

it('persists the receipt, property, and entry on the happy path', function () {
    $this->actingAs($this->user)
        ->postJson("/collections/transaction-entry/{$this->formStock->id}/or-rpt", validOrRptPayload())
        ->assertOk()
        ->assertJsonPath('message', 'Transaction saved successfully.');

    expect(OrRptTransaction::count())->toBe(1);
    expect(RptProperty::where('tax_declaration_number', 'TD-001')->exists())->toBeTrue();
    expect(OrRptTransaction::first()->entries()->count())->toBe(1);
});

it('rejects a receipt with no entries', function () {
    $this->actingAs($this->user)
        ->postJson("/collections/transaction-entry/{$this->formStock->id}/or-rpt", validOrRptPayload(['entries' => []]))
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('entries');
});

it('rejects an entry with both payment schemes implied (full + installment_quarter)', function () {
    $payload = validOrRptPayload();
    $payload['entries'][0]['payment_scheme'] = 'full';
    $payload['entries'][0]['installment_quarter'] = 2; // contradiction: full + a quarter

    $this->actingAs($this->user)
        ->postJson("/collections/transaction-entry/{$this->formStock->id}/or-rpt", $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('entries.0.installment_quarter');
});

it('rejects when amount_paid does not equal the sum of entry totals', function () {
    $this->actingAs($this->user)
        ->postJson("/collections/transaction-entry/{$this->formStock->id}/or-rpt", validOrRptPayload(['amount_paid' => 999]))
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('amount_paid');
});

it('reuses an existing property and records a second installment', function () {
    $property = RptProperty::create([
        'tax_declaration_number' => 'TD-001',
        'declared_owner' => 'Juan Dela Cruz',
        'assessed_value_total' => 100000,
        'annual_tax_due' => 2000,
    ]);

    $this->actingAs($this->user)
        ->postJson("/collections/transaction-entry/{$this->formStock->id}/or-rpt", validOrRptPayload())
        ->assertOk();

    expect(RptProperty::where('tax_declaration_number', 'TD-001')->count())->toBe(1);
    expect($property->fresh()->entries()->count())->toBe(1);
});
