<?php

use App\Models\FormBatch;
use App\Models\FormStock;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function ackCollector(string $name = 'Juan Dela Cruz'): User
{
    $role = Role::firstOrCreate(['slug' => 'collector'], ['name' => 'Collector']);
    $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function ackAdmin(string $name = 'Admin User'): User
{
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function ackFormStock(string $code = 'Form 58'): FormStock
{
    return FormStock::create([
        'qty' => 0,
        'form_name' => 'Burial',
        'form_code' => $code,
        'added_date' => now()->toDateString(),
        'added_by' => 'System',
    ]);
}

function ackBatch(FormStock $stock, ?string $assignedTo = 'Juan Dela Cruz', ?\Illuminate\Support\Carbon $receivedAt = null): FormBatch
{
    return $stock->batches()->create([
        'registration_date' => now(),
        'purchase_date' => now(),
        'starting_serial_number' => '0000001',
        'ending_serial_number' => '0000005',
        'added_by' => 'Admin User',
        'assigned_to' => $assignedTo,
        'received_at' => $receivedAt,
    ]);
}

it('flags a batch as needing acknowledgement when assigned but not yet received', function () {
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', null);

    expect($batch->needsAcknowledgement())->toBeTrue();
});

it('does not flag a batch as needing acknowledgement once received', function () {
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', now());

    expect($batch->needsAcknowledgement())->toBeFalse();
});

it('does not flag an unassigned batch as needing acknowledgement', function () {
    $batch = ackBatch(ackFormStock(), null, null);

    expect($batch->needsAcknowledgement())->toBeFalse();
});
