<?php

use App\Models\BatchRequest;
use App\Models\FormStock;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeUserWithRole(string $slug, string $name): User
{
    $role = Role::firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);
    $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function makeCollector(string $name = 'Juan Dela Cruz'): User
{
    return makeUserWithRole('collector', $name);
}

function makeAdminUser(string $name = 'Admin User'): User
{
    return makeUserWithRole('admin', $name);
}

function makeFormStock(string $code = 'BIR0016'): FormStock
{
    return FormStock::firstOrCreate(
        ['form_code' => $code],
        ['qty' => 0, 'form_name' => 'Cedula (Individual)', 'added_date' => now()->toDateString(), 'added_by' => 'System'],
    );
}

it('creates a batch request with working relations', function () {
    $collector = makeCollector();
    $stock = makeFormStock();

    $request = BatchRequest::create([
        'form_stock_id' => $stock->id,
        'requested_by' => $collector->id,
        'quantity' => 10,
        'status' => 'pending',
    ]);

    expect($request->formStock->id)->toBe($stock->id);
    expect($request->requestedByUser->id)->toBe($collector->id);
    expect($stock->batchRequests()->count())->toBe(1);
});

it('returns the created batch from applyBatch', function () {
    $stock = makeFormStock();

    $batch = $stock->applyBatch([
        'registration_year' => 2026, 'registration_month' => 1, 'registration_day' => 1,
        'purchase_year' => 2026, 'purchase_month' => 1, 'purchase_day' => 1,
        'starting_serial_number' => '2026-00001',
        'ending_serial_number' => '2026-00010',
    ], 'Admin User');

    expect($batch)->toBeInstanceOf(\App\Models\FormBatch::class);
    expect($batch->starting_serial_number)->toBe('2026-00001');
});

it('lets a collector submit a batch request', function () {
    $collector = makeCollector();
    $stock = makeFormStock();

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/{$stock->id}/batch-requests", [
            'quantity' => 5,
            'note' => 'Running low on stock',
        ])
        ->assertOk()
        ->assertJsonPath('message', 'Batch request submitted successfully.');

    expect(BatchRequest::where('form_stock_id', $stock->id)->where('status', 'pending')->count())->toBe(1);
    expect(BatchRequest::first()->note)->toBe('Running low on stock');
});

it('blocks a duplicate pending batch request from the same collector', function () {
    $collector = makeCollector();
    $stock = makeFormStock();

    $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 3, 'status' => 'pending']);

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/{$stock->id}/batch-requests", ['quantity' => 5])
        ->assertStatus(422)
        ->assertJsonPath('message', fn ($m) => str_contains($m, 'already have a pending'));

    expect(BatchRequest::where('form_stock_id', $stock->id)->count())->toBe(1);
});

it('rejects a batch request with an invalid quantity', function () {
    $collector = makeCollector();
    $stock = makeFormStock();

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/{$stock->id}/batch-requests", ['quantity' => 0])
        ->assertStatus(422);
});
