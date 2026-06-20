<?php

use App\Models\OrRptTransaction;
use App\Models\OrRptTransactionEntry;
use App\Models\RptProperty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
});

it('returns a known property with its paid quarters', function () {
    $property = RptProperty::create([
        'tax_declaration_number' => 'TD-001',
        'declared_owner' => 'Juan Dela Cruz',
        'location' => 'Brgy. Sablayan',
        'assessed_value_land' => 60000,
        'assessed_value_improvement' => 40000,
        'assessed_value_total' => 100000,
        'annual_tax_due' => 2000,
    ]);

    $receipt = OrRptTransaction::create([
        'form_stock_id' => makeOrRptFormStock()->id,
        'certificate_number' => '0000001',
        'client_name' => 'Juan Dela Cruz',
        'amount_paid' => 500,
    ]);

    OrRptTransactionEntry::create([
        'or_rpt_transaction_id' => $receipt->id,
        'rpt_property_id' => $property->id,
        'payment_scheme' => 'installment',
        'installment_quarter' => 1,
        'amount' => 500,
    ]);

    $this->actingAs($this->user)
        ->getJson('/rpt-properties/TD-001')
        ->assertOk()
        ->assertJsonPath('found', true)
        ->assertJsonPath('property.declared_owner', 'Juan Dela Cruz')
        ->assertJsonPath('paid_quarters', [1]);
});

it('returns found=false for an unknown TD number', function () {
    $this->actingAs($this->user)
        ->getJson('/rpt-properties/UNKNOWN')
        ->assertOk()
        ->assertJsonPath('found', false);
});
