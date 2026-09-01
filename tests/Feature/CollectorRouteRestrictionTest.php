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
