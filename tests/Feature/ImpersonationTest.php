<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function impUser(string $slug, string $name): User
{
    $role = Role::firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);
    $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

beforeEach(function () {
    config(['app.impersonate.enabled' => true, 'app.impersonate.collector' => 'Ramon Torres']);
});

it('lets an admin switch into the configured collector and back', function () {
    $admin = impUser('admin', 'Admin User');
    $ramon = impUser('collector', 'Ramon Torres');

    $this->actingAs($admin)->post(route('impersonate.start'))->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($ramon);
    expect(session('impersonator_id'))->toBe($admin->id);

    $this->post(route('impersonate.stop'))->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($admin);
    expect(session()->has('impersonator_id'))->toBeFalse();
});

it('is not available when disabled', function () {
    config(['app.impersonate.enabled' => false]);
    $admin = impUser('admin', 'Admin User');
    impUser('collector', 'Ramon Torres');

    $this->actingAs($admin)->post(route('impersonate.start'))->assertNotFound();
});

it('refuses non-admins', function () {
    impUser('collector', 'Ramon Torres');
    $other = impUser('collector', 'Juan Dela Cruz');

    $this->actingAs($other)->post(route('impersonate.start'))->assertForbidden();
});

it('refuses a disabled or non-collector target', function () {
    $admin = impUser('admin', 'Admin User');
    $ramon = impUser('collector', 'Ramon Torres');
    $ramon->update(['status' => User::STATUS_DISABLED]);

    $this->actingAs($admin)->post(route('impersonate.start'))->assertStatus(422);
});

it('cannot stop impersonation when none is active', function () {
    $admin = impUser('admin', 'Admin User');

    $this->actingAs($admin)->post(route('impersonate.stop'))->assertNotFound();
});
