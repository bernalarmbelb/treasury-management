<?php

use App\Models\FormBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeOrRptBatch(\App\Models\FormStock $stock): FormBatch
{
    return $stock->batches()->create([
        'registration_date' => now(),
        'purchase_date' => now(),
        'starting_serial_number' => 'ORRPT000',
        'ending_serial_number' => 'ORRPT001',
        'added_by' => 'Tester',
    ]);
}

it('does not count synthetic (non-prefixed) serials against an ORRPT batch', function () {
    $stock = makeOrRptFormStock();
    makeOrRptBatch($stock);

    // Legacy receipt saved by the old code with a synthetic id-counter serial.
    $stock->orRptTransactions()->create([
        'certificate_number' => '0000001',
        'client_name' => 'Legacy',
        'amount_paid' => 0,
    ]);

    $batch = FormBatch::first()->fresh('formStock');

    expect($batch->usedQty())->toBe(0);
    expect($batch->nextAvailableSerialNumber())->toBe('000');
});

it('counts a properly-prefixed ORRPT serial against the batch', function () {
    $stock = makeOrRptFormStock();
    makeOrRptBatch($stock);

    $stock->orRptTransactions()->create([
        'certificate_number' => 'ORRPT000',
        'client_name' => 'Real',
        'amount_paid' => 0,
    ]);

    $batch = FormBatch::first()->fresh('formStock');

    expect($batch->usedQty())->toBe(1);
    expect($batch->nextAvailableSerialNumber())->toBe('001');
});
