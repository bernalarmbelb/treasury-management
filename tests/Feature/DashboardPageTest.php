<?php
use App\Models\User;
use App\Models\Role;
use App\Models\TransactionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the dashboard for an authenticated user with the real controller payload', function () {
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    TransactionLog::create([
        'serial_number' => 'TEST-0001',
        'payee' => 'Jane Doe',
        'transacted_at' => now(),
        'form_type' => 'Form 58',
        'status' => 'Completed',
        'amount' => 1500.00,
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertViewIs('dashboard');

    foreach (['range', 'cash', 'collections', 'disbursed', 'exceptions', 'trend', 'payments', 'reconciliation', 'forms', 'activity'] as $key) {
        $response->assertViewHas($key);
    }

    expect($response->viewData('collections')['total'])->toEqual(1500.00);
});

it('honors the range query parameter', function () {
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)->get('/?range=today')->assertOk()->assertViewHas('range', 'today');
    $this->actingAs($user)->get('/?range=week')->assertOk()->assertViewHas('range', 'week');
    $this->actingAs($user)->get('/')->assertOk()->assertViewHas('range', 'month');
});
