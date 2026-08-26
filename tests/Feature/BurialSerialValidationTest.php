<?php

use App\Models\FormStock;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeBurialStockWithBatch(string $start = '0000001', string $end = '0000010'): FormStock
{
    $stock = FormStock::create(['qty' => 0, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => now()->toDateString(), 'added_by' => 'T']);
    $stock->batches()->create(['registration_date' => now(), 'purchase_date' => now(), 'starting_serial_number' => $start, 'ending_serial_number' => $end, 'added_by' => 'T']);

    return $stock;
}

it('rejects a burial serial number outside the purchased batch range', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = makeBurialStockWithBatch();

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/burial", [
        'certificate_number' => '9999999',
        'deceased_name' => 'Juan Dela Cruz',
        'payment_method' => 'cash',
    ])->assertStatus(422)->assertJsonFragment(['message' => 'Serial number 9999999 was not found in the available stock. Cannot proceed.']);

    expect(TransactionLog::count())->toBe(0);
});

it('blocks a duplicate burial serial number already recorded', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = makeBurialStockWithBatch();

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/burial", [
        'certificate_number' => '0000001',
        'deceased_name' => 'Juan Dela Cruz',
        'payment_method' => 'cash',
    ])->assertOk();

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/burial", [
        'certificate_number' => '0000001',
        'deceased_name' => 'Someone Else',
        'payment_method' => 'cash',
    ])->assertStatus(422)->assertJsonFragment(['message' => 'Serial number 0000001 is already taken.']);

    expect(TransactionLog::count())->toBe(1);
});

it('advances to the next available serial after one is used', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = makeBurialStockWithBatch();

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/burial", [
        'certificate_number' => '0000001',
        'deceased_name' => 'Juan Dela Cruz',
        'payment_method' => 'cash',
    ])->assertOk();

    expect($stock->fresh()->nextAvailableBatch()?->nextAvailableSerialNumber())->toBe('0000002');
    expect($stock->fresh()->availableQty())->toBe(9);
});
