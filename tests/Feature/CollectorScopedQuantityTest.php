<?php

use App\Models\FormStock;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function qtyCollector(string $name = 'Juan Dela Cruz'): User
{
    $role = Role::firstOrCreate(['slug' => 'collector'], ['name' => 'Collector']);
    $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

function qtyAdmin(): User
{
    $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $user->roles()->sync([$role->id]);

    return $user;
}

/**
 * A Burial (Form 58) stock with two batches: 5 serials assigned to
 * "Juan Dela Cruz", 20 serials unassigned — no transactions recorded, so
 * remainingQty() equals the full range for both.
 */
function qtyFormStock(): FormStock
{
    $stock = FormStock::create([
        'qty' => 0,
        'form_name' => 'Burial',
        'form_code' => 'Form 58',
        'added_date' => now()->toDateString(),
        'added_by' => 'System',
    ]);

    $stock->batches()->create([
        'registration_date' => now(),
        'purchase_date' => now(),
        'starting_serial_number' => '0000001',
        'ending_serial_number' => '0000005',
        'added_by' => 'System',
        'assigned_to' => 'Juan Dela Cruz',
    ]);

    $stock->batches()->create([
        'registration_date' => now(),
        'purchase_date' => now(),
        'starting_serial_number' => '0001000',
        'ending_serial_number' => '0001019',
        'added_by' => 'System',
        'assigned_to' => null,
    ]);

    return $stock;
}

it('sums availableQtyForCollector from only the batches assigned to that collector', function () {
    $stock = qtyFormStock();

    expect($stock->availableQtyForCollector('Juan Dela Cruz'))->toBe(5);
    expect($stock->availableQtyForCollector('Someone Else'))->toBe(0);
    expect($stock->availableQty())->toBe(25);
});

/**
 * Extracts the Qty. column value for the Burial row from a rendered ORAF
 * forms-table response. Regex, not assertSee, because the page's own
 * "Rows per page" <select> renders literal <option>25</option> among its
 * [10, 25, 50, 100] choices — a naive substring/`>25<` check on the raw
 * HTML would false-positive against that unrelated control.
 */
function burialQtyFromResponse(\Illuminate\Testing\TestResponse $response): int
{
    preg_match('/<td class="qty-[a-z]+">(\d+)<\/td>\s*<td>Burial<\/td>/', $response->getContent(), $matches);

    expect($matches)->not->toBeEmpty('Could not find the Burial row\'s Qty. cell in the response.');

    return (int) $matches[1];
}

it('shows a collector their own assigned quantity in the ORAF forms list, not the global total', function () {
    $collector = qtyCollector();
    qtyFormStock();

    $response = $this->actingAs($collector)->get('/official-receipts-accountable-forms');

    $response->assertOk();
    $response->assertSee('Burial');
    expect(burialQtyFromResponse($response))->toBe(5);
});

it('shows an admin the global total quantity in the ORAF forms list', function () {
    $admin = qtyAdmin();
    qtyFormStock();

    $response = $this->actingAs($admin)->get('/official-receipts-accountable-forms');

    $response->assertOk();
    $response->assertSee('Burial');
    expect(burialQtyFromResponse($response))->toBe(25);
});

it('scopes the Quick Entry footer bar quantity to the collector', function () {
    $collector = qtyCollector();
    qtyFormStock();

    $response = $this->actingAs($collector)->get('/official-receipts-accountable-forms');

    $response->assertOk();
    $response->assertSee('Burial');
    $response->assertSee('5 left');
    $response->assertDontSee('25 left');
});

it('shows the global Quick Entry quantity for an admin', function () {
    $admin = qtyAdmin();
    qtyFormStock();

    $response = $this->actingAs($admin)->get('/official-receipts-accountable-forms');

    $response->assertOk();
    $response->assertSee('25 left');
});

it('scopes the report-logs batch list to only the collectors own assigned batches', function () {
    $collector = qtyCollector();
    $stock = qtyFormStock();

    $response = $this->actingAs($collector)->get("/official-receipts-accountable-forms/{$stock->id}/report-logs");

    $response->assertOk();
    $response->assertSee('0000001');
    $response->assertDontSee('0001000');
});

it('shows every batch in the report-logs list for an admin', function () {
    $admin = qtyAdmin();
    $stock = qtyFormStock();

    $response = $this->actingAs($admin)->get("/official-receipts-accountable-forms/{$stock->id}/report-logs");

    $response->assertOk();
    $response->assertSee('0000001');
    $response->assertSee('0001000');
});
