<?php
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the dashboard for an authenticated user', function () {
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Collections');
});
