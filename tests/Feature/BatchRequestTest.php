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
