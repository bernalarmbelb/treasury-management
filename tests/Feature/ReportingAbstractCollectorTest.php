<?php

use App\Models\FormStock;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function raCollector(string $name = 'Juan Dela Cruz'): User
{
    $role = Role::firstOrCreate(['slug' => 'collector'], ['name' => 'Collector']);
    $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function raAdmin(): User
{
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

it('shows a collector only the My Batch Accountability Report entry', function () {
    $collector = raCollector();

    $response = $this->actingAs($collector)->get('/reporting-abstract');

    $response->assertOk();
    $response->assertSee('My Batch Accountability Report');
    $response->assertDontSee("Treasurer's Monthly Report");
    $response->assertDontSee('Consolidated Report of Accountability');
    $response->assertDontSee('Reports of Checks Issued');
});

it('shows an admin the full reporting-abstract list, without the collector-only entry', function () {
    $admin = raAdmin();

    $response = $this->actingAs($admin)->get('/reporting-abstract');

    $response->assertOk();
    $response->assertSee("Treasurer's Monthly Report");
    $response->assertDontSee('My Batch Accountability Report');
});

it('renders the collectors own batch on the My Batch Accountability Report page, scoped to the period', function () {
    $collector = raCollector();

    $stock = FormStock::create([
        'qty' => 0,
        'form_name' => 'Burial',
        'form_code' => 'Form 58',
        'added_date' => now()->toDateString(),
        'added_by' => 'System',
    ]);

    $stock->batches()->create([
        'registration_date' => '2026-08-01',
        'purchase_date' => '2026-08-01',
        'starting_serial_number' => '0000001',
        'ending_serial_number' => '0000005',
        'added_by' => 'System',
        'assigned_to' => 'Juan Dela Cruz',
    ]);

    $stock->batches()->create([
        'registration_date' => '2026-08-01',
        'purchase_date' => '2026-08-01',
        'starting_serial_number' => '0001000',
        'ending_serial_number' => '0001019',
        'added_by' => 'System',
        'assigned_to' => 'Someone Else',
    ]);

    $response = $this->actingAs($collector)->get('/reporting-abstract/my-batch-report?month=8&year=2026');

    $response->assertOk();
    $response->assertSee('MY BATCH ACCOUNTABILITY REPORT');
    $response->assertSee('Burial');
    // Batch 1 (assigned to this collector, qty 5) is included; batch 2
    // (assigned to "Someone Else", qty 20) must not appear anywhere.
    $response->assertSee('0000001');
    $response->assertDontSee('0001000');
    $response->assertDontSee('Someone Else');
});

it('shows an empty state when the collector has no batches in the selected period', function () {
    $collector = raCollector('Nobody Here');

    $response = $this->actingAs($collector)->get('/reporting-abstract/my-batch-report?month=1&year=2020');

    $response->assertOk();
    $response->assertSee('No batches assigned to you for this period.');
});
