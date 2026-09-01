<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function collectorUser(): User
{
    $role = Role::firstOrCreate(['slug' => 'collector'], ['name' => 'Collector']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function adminUser(): User
{
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function collectorRouteFormStock(string $code = 'BIR0016'): \App\Models\FormStock
{
    return \App\Models\FormStock::firstOrCreate(
        ['form_code' => $code],
        ['qty' => 0, 'form_name' => 'Cedula (Individual)', 'added_date' => now()->toDateString(), 'added_by' => 'System'],
    );
}

it('blocks a collector from bank deposit and reconciliation routes', function () {
    $collector = collectorUser();

    $this->actingAs($collector)->get('/bank-deposit-reconciliation')->assertStatus(403);
    $this->actingAs($collector)->get('/bank-deposit-reconciliation/incoming')->assertStatus(403);
});

it('blocks a collector from cheque management routes', function () {
    $collector = collectorUser();

    $this->actingAs($collector)->get('/cheque-management')->assertStatus(403);
    $this->actingAs($collector)->get('/cheque-management/create')->assertStatus(403);
});

it('blocks a collector from user management routes', function () {
    $collector = collectorUser();

    $this->actingAs($collector)->get('/user-management')->assertStatus(403);
});

it('does not block an admin from those routes', function () {
    $admin = adminUser();

    $this->actingAs($admin)->get('/bank-deposit-reconciliation')->assertOk();
    $this->actingAs($admin)->get('/cheque-management')->assertOk();
    $this->actingAs($admin)->get('/user-management')->assertOk();
});

it('hides the restricted nav links for a collector but shows them for an admin', function () {
    $collector = collectorUser();
    $admin = adminUser();

    $this->actingAs($collector)->get('/collections')->assertDontSee('Banks Deposit & Reconciliation', false)->assertDontSee('Cheque Management')->assertDontSee('User Management');
    $this->actingAs($admin)->get('/collections')->assertSee('Banks Deposit & Reconciliation', false)->assertSee('Cheque Management')->assertSee('User Management');
});

it('blocks a collector from hitting the collections transaction-entry add-batch route directly', function () {
    $collector = collectorUser();
    $stock = collectorRouteFormStock();

    $this->actingAs($collector)
        ->postJson("/collections/transaction-entry/{$stock->id}/batches", [
            'registration_month' => 1, 'registration_day' => 1, 'registration_year' => 2026,
            'purchase_month' => 1, 'purchase_day' => 1, 'purchase_year' => 2026,
            'starting_serial_number' => '2026-00001',
            'ending_serial_number' => '2026-00010',
        ])
        ->assertStatus(403);
});

it('blocks a collector from directly patching the batches assign route', function () {
    $collector = collectorUser();
    $stock = collectorRouteFormStock();
    $batch = $stock->applyBatch([
        'registration_year' => 2026, 'registration_month' => 1, 'registration_day' => 1,
        'purchase_year' => 2026, 'purchase_month' => 1, 'purchase_day' => 1,
        'starting_serial_number' => '2026-00001',
        'ending_serial_number' => '2026-00010',
    ], 'Admin User');

    $this->actingAs($collector)
        ->patchJson("/official-receipts-accountable-forms/batches/{$batch->id}/assign", [
            'assigned_to' => $collector->name,
        ])
        ->assertStatus(403)
        ->assertJsonPath('message', 'Unauthorized.');

    expect($batch->fresh()->assigned_to)->toBeNull();
});

it('excludes Reports of Checks Issued from the reporting-abstract list for a collector but keeps it for an admin', function () {
    $collector = collectorUser();
    $admin = adminUser();

    $this->actingAs($collector)->get('/reporting-abstract')->assertOk()->assertDontSee('Reports of Checks Issued');
    $this->actingAs($admin)->get('/reporting-abstract')->assertOk()->assertSee('Reports of Checks Issued');
});

it('hides the bank-deposit-reconciliation and new-cheque dashboard links for a collector but shows them for an admin', function () {
    $collector = collectorUser();
    $admin = adminUser();

    $this->actingAs($collector)->get('/')
        ->assertOk()
        ->assertDontSee(route('bank-deposit-reconciliation'), false)
        ->assertDontSee(route('cheque-management.create'), false);

    $this->actingAs($admin)->get('/')
        ->assertOk()
        ->assertSee(route('bank-deposit-reconciliation'), false)
        ->assertSee(route('cheque-management.create'), false);
});

it('blocks a collector from viewing an archived user record directly', function () {
    $collector = collectorUser();
    $otherUser = User::factory()->create(['status' => User::STATUS_ARCHIVED]);

    $this->actingAs($collector)
        ->get("/archive-records/users/{$otherUser->id}")
        ->assertStatus(403);
});

it('blocks a collector from unarchiving a user directly', function () {
    $collector = collectorUser();
    $otherUser = User::factory()->create(['status' => User::STATUS_ARCHIVED]);

    $this->actingAs($collector)
        ->postJson("/archive-records/users/{$otherUser->id}/unarchive")
        ->assertStatus(403)
        ->assertJsonPath('message', 'Unauthorized.');

    expect($otherUser->fresh()->status)->toBe(User::STATUS_ARCHIVED);
});

it('forces a collector requesting the user-management archives tab back to collection-management', function () {
    $collector = collectorUser();
    $admin = adminUser();

    $this->actingAs($collector)
        ->get('/archive-records?tab=user-management')
        ->assertOk()
        ->assertSee('Collection Management')
        ->assertDontSee('User Management');

    $this->actingAs($admin)
        ->get('/archive-records?tab=user-management')
        ->assertOk()
        ->assertSee('User Management');
});
