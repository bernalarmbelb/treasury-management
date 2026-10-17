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

it('does not flag a batch with empty-string assigned_to as needing acknowledgement', function () {
    $batch = ackBatch(ackFormStock(), '', null);

    expect($batch->needsAcknowledgement())->toBeFalse();
});

it('resets received_at when a batch is reassigned to a different collector', function () {
    $admin = ackAdmin();
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', now());

    $this->actingAs($admin)
        ->patchJson("/official-receipts-accountable-forms/batches/{$batch->id}/assign", [
            'assigned_to' => 'Maria Santos',
        ])
        ->assertOk();

    $batch->refresh();
    expect($batch->assigned_to)->toBe('Maria Santos');
    expect($batch->received_at)->toBeNull();
});

it('keeps received_at when the assign request repeats the same collector', function () {
    $admin = ackAdmin();
    $receivedAt = now()->subDay();
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', $receivedAt);

    $this->actingAs($admin)
        ->patchJson("/official-receipts-accountable-forms/batches/{$batch->id}/assign", [
            'assigned_to' => 'Juan Dela Cruz',
        ])
        ->assertOk();

    $batch->refresh();
    expect($batch->received_at?->timestamp)->toBe($receivedAt->timestamp);
});

it('resets received_at when a batch is unassigned', function () {
    $admin = ackAdmin();
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', now());

    $this->actingAs($admin)
        ->patchJson("/official-receipts-accountable-forms/batches/{$batch->id}/assign", [
            'assigned_to' => '',
        ])
        ->assertOk();

    $batch->refresh();
    expect($batch->assigned_to)->toBeNull();
    expect($batch->received_at)->toBeNull();
});

it('excludes a pending (unacknowledged) batch quantity from availableQtyForCollector', function () {
    $stock = ackFormStock();
    ackBatch($stock, 'Juan Dela Cruz', null); // pending — 5 serials, not yet received

    expect($stock->availableQtyForCollector('Juan Dela Cruz'))->toBe(0);
    // The global total (used by Admin's view) is unaffected by acknowledgement.
    expect($stock->availableQty())->toBe(5);
});

it('counts a received batch quantity toward availableQtyForCollector', function () {
    $stock = ackFormStock();
    ackBatch($stock, 'Juan Dela Cruz', now());

    expect($stock->availableQtyForCollector('Juan Dela Cruz'))->toBe(5);
});

it('lets the assigned collector mark their own batch as received', function () {
    $collector = ackCollector('Juan Dela Cruz');
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', null);

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/batches/{$batch->id}/receive")
        ->assertOk();

    expect($batch->fresh()->received_at)->not->toBeNull();
});

it('blocks a different collector from marking someone elses batch as received', function () {
    $collector = ackCollector('Someone Else');
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', null);

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/batches/{$batch->id}/receive")
        ->assertStatus(403);

    expect($batch->fresh()->received_at)->toBeNull();
});

it('blocks an admin from marking a collectors batch as received on their behalf', function () {
    $admin = ackAdmin();
    $batch = ackBatch(ackFormStock(), 'Juan Dela Cruz', null);

    $this->actingAs($admin)
        ->postJson("/official-receipts-accountable-forms/batches/{$batch->id}/receive")
        ->assertStatus(403);

    expect($batch->fresh()->received_at)->toBeNull();
});

it('shows a Received button to the assigned collector on their pending batch row', function () {
    $collector = ackCollector('Juan Dela Cruz');
    $stock = ackFormStock();
    $batch = ackBatch($stock, 'Juan Dela Cruz', null);

    $response = $this->actingAs($collector)->get("/official-receipts-accountable-forms/{$stock->id}/report-logs");

    $response->assertOk();
    $response->assertSee('Pending Acknowledgement');
    $response->assertSee('js-receive-batch', false);
    $response->assertSee('data-batch-id="' . $batch->id . '"', false);
});

it('does not show a Received button once the batch is received', function () {
    $collector = ackCollector('Juan Dela Cruz');
    $stock = ackFormStock();
    ackBatch($stock, 'Juan Dela Cruz', now());

    $response = $this->actingAs($collector)->get("/official-receipts-accountable-forms/{$stock->id}/report-logs");

    $response->assertOk();
    $response->assertSee('Received');
    $response->assertDontSee('js-receive-batch', false);
});

it('never shows a Received button to an admin viewing the same row', function () {
    $admin = ackAdmin();
    $stock = ackFormStock();
    ackBatch($stock, 'Juan Dela Cruz', null);

    $response = $this->actingAs($admin)->get("/official-receipts-accountable-forms/{$stock->id}/report-logs");

    $response->assertOk();
    $response->assertSee('Pending Acknowledgement');
    $response->assertDontSee('js-receive-batch', false);
});
