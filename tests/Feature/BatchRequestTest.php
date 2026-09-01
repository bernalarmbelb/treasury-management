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

it('lets an admin fulfill a batch request via the existing add-batch route, linking and assigning it', function () {
    $admin = makeAdminUser();
    $collector = makeCollector('Maria Santos');
    $stock = makeFormStock();
    $batchRequest = $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 10, 'status' => 'pending']);

    $this->actingAs($admin)
        ->postJson("/official-receipts-accountable-forms/{$stock->id}/batches", [
            'registration_month' => 1, 'registration_day' => 1, 'registration_year' => 2026,
            'purchase_month' => 1, 'purchase_day' => 1, 'purchase_year' => 2026,
            'starting_serial_number' => '2026-00001',
            'ending_serial_number' => '2026-00010',
            'batch_request_id' => $batchRequest->id,
        ])
        ->assertOk();

    $batchRequest->refresh();
    expect($batchRequest->status)->toBe('approved');
    expect($batchRequest->resultingBatch->assigned_to)->toBe('Maria Santos');
});

it('rejects fulfilling with a batch_request_id that is not pending', function () {
    $admin = makeAdminUser();
    $collector = makeCollector();
    $stock = makeFormStock();
    $batchRequest = $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 10, 'status' => 'rejected']);

    $this->actingAs($admin)
        ->postJson("/official-receipts-accountable-forms/{$stock->id}/batches", [
            'registration_month' => 1, 'registration_day' => 1, 'registration_year' => 2026,
            'purchase_month' => 1, 'purchase_day' => 1, 'purchase_year' => 2026,
            'starting_serial_number' => '2026-00001',
            'ending_serial_number' => '2026-00010',
            'batch_request_id' => $batchRequest->id,
        ])
        ->assertStatus(422)
        ->assertJsonPath('message', 'This batch request is no longer pending.');
});

it('blocks a collector from hitting the add-batch route directly', function () {
    $collector = makeCollector();
    $stock = makeFormStock();

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/{$stock->id}/batches", [
            'registration_month' => 1, 'registration_day' => 1, 'registration_year' => 2026,
            'purchase_month' => 1, 'purchase_day' => 1, 'purchase_year' => 2026,
            'starting_serial_number' => '2026-00001',
            'ending_serial_number' => '2026-00010',
        ])
        ->assertStatus(403);
});

it('lets an admin reject a pending batch request', function () {
    $admin = makeAdminUser();
    $collector = makeCollector();
    $stock = makeFormStock();
    $batchRequest = $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 10, 'status' => 'pending']);

    $this->actingAs($admin)
        ->postJson("/official-receipts-accountable-forms/batch-requests/{$batchRequest->id}/reject")
        ->assertOk()
        ->assertJsonPath('message', 'Batch request rejected.');

    expect($batchRequest->fresh()->status)->toBe('rejected');
});

it('blocks a non-admin from rejecting a batch request', function () {
    $collector = makeCollector();
    $stock = makeFormStock();
    $batchRequest = $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 10, 'status' => 'pending']);

    $this->actingAs($collector)
        ->postJson("/official-receipts-accountable-forms/batch-requests/{$batchRequest->id}/reject")
        ->assertStatus(403);

    expect($batchRequest->fresh()->status)->toBe('pending');
});

it('only includes pending batch requests for an admin viewing report logs', function () {
    $admin = makeAdminUser();
    $collector = makeCollector();
    $stock = makeFormStock();
    $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 10, 'status' => 'pending']);

    $this->actingAs($admin)
        ->get("/official-receipts-accountable-forms/{$stock->id}/report-logs")
        ->assertOk()
        ->assertSee('Pending Batch Requests')
        ->assertSee($collector->name);
});

it('does not show the pending batch requests panel to a collector', function () {
    $collector = makeCollector();
    $stock = makeFormStock();
    $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 10, 'status' => 'pending']);

    $this->actingAs($collector)
        ->get("/official-receipts-accountable-forms/{$stock->id}/report-logs")
        ->assertOk()
        ->assertDontSee('Pending Batch Requests');
});

it('includes pending batch requests in the admin notification count and list', function () {
    $admin = makeAdminUser();
    $collector = makeCollector('Pedro Reyes');
    $stock = makeFormStock();
    $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 7, 'status' => 'pending']);

    $this->actingAs($admin)->getJson('/notifications/count')->assertJsonPath('count', 1);

    $response = $this->actingAs($admin)->getJson('/notifications')->assertOk();
    $items = $response->json('items');
    expect(collect($items)->firstWhere('type', 'batch_request'))->not->toBeNull();
    expect(collect($items)->firstWhere('type', 'batch_request')['payee'])->toContain('Pedro Reyes');
});

it('includes rejected batch requests in the collectors notification list until marked seen', function () {
    $admin = makeAdminUser();
    $collector = makeCollector();
    $stock = makeFormStock();
    $batchRequest = $stock->batchRequests()->create(['requested_by' => $collector->id, 'quantity' => 7, 'status' => 'rejected', 'reviewed_by' => $admin->id, 'reviewed_at' => now()]);

    $this->actingAs($collector)->getJson('/notifications/count')->assertJsonPath('count', 1);

    $this->actingAs($collector)->postJson('/notifications/mark-seen')->assertOk();

    expect($batchRequest->fresh()->notified_at)->not->toBeNull();
    $this->actingAs($collector)->getJson('/notifications/count')->assertJsonPath('count', 0);
});
