<?php

use App\Models\User;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
    Artisan::call('db:seed', ['--class' => 'EngineerSeeder']);

    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

test('index page loads for authenticated user', function () {
    $response = $this->get(route('dashboard.villas.index'));

    $response->assertOk();
});

test('create page loads for authenticated user', function () {
    $response = $this->get(route('dashboard.villas.create'));

    $response->assertOk();
});

test('store creates villa and redirects to show', function () {
    $villaType = VillaType::first();

    $response = $this->post(route('dashboard.villas.store'), [
        'code' => 'D-NEW-001',
        'villa_type_id' => $villaType->id,
    ]);

    $villa = Villa::where('code', 'D-NEW-001')->first();

    expect($villa)->not->toBeNull();
    $response->assertRedirect(route('dashboard.villas.show', $villa));
    $this->assertDatabaseHas('villas', ['code' => 'D-NEW-001']);
});

/**
 * Mirrors preparePayload() in resources/js/pages/dashboard/villas/Create.tsx:
 * every optional field is sent as an explicit null when left blank.
 *
 * @return array<string, mixed>
 */
function blankVillaFormPayload(string $code, int $villaTypeId): array
{
    return [
        'code' => $code,
        'villa_type_id' => $villaTypeId,
        'is_sold' => false,
        'customer_id' => null,
        'customer_name' => null,
        'sale_date' => null,
        'engineer_id' => null,
        'current_stage_id' => null,
        'status_option_id' => null,
        'structural_status_id' => null,
        'finishing_status_id' => null,
        'facade_status_id' => null,
        'completion_pct' => null,
        'planned_start' => null,
        'planned_finish' => null,
        'actual_start' => null,
        'actual_finish' => null,
        'acc_concrete_qty' => null,
        'acc_steel_qty' => null,
    ];
}

test('store accepts the form payload with every optional field left blank', function () {
    $type = VillaType::first();

    $response = $this->post(
        route('dashboard.villas.store'),
        blankVillaFormPayload('D-V-BLANK-001', $type->id)
    );

    $villa = Villa::where('code', 'D-V-BLANK-001')->first();

    expect($villa)->not->toBeNull();
    $response->assertRedirect(route('dashboard.villas.show', $villa));

    // completion_pct / acc_concrete_qty / acc_steel_qty are NOT NULL DEFAULT 0,
    // so a blank input has to land as 0 rather than null.
    expect($villa->completion_pct)->toBe(0.0)
        ->and($villa->acc_concrete_qty)->toBe(0.0)
        ->and($villa->acc_steel_qty)->toBe(0.0);
});

test('update accepts the form payload with every optional field left blank', function () {
    $type = VillaType::first();
    $villa = Villa::create([
        'code' => 'D-V-BLANK-002',
        'villa_type_id' => $type->id,
        'completion_pct' => 42,
    ]);

    $response = $this->put(
        route('dashboard.villas.update', $villa),
        blankVillaFormPayload('D-V-BLANK-002', $type->id)
    );

    $response->assertRedirect(route('dashboard.villas.show', $villa));

    expect($villa->fresh()->completion_pct)->toBe(0.0);
});

test('edit page loads with villa data', function () {
    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'D-EDIT-001', 'villa_type_id' => $villaType->id]);

    $response = $this->get(route('dashboard.villas.edit', $villa));

    $response->assertOk();
});

test('update modifies villa and redirects to show', function () {
    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'D-UPD-001', 'villa_type_id' => $villaType->id]);

    $response = $this->put(route('dashboard.villas.update', $villa), [
        'code' => 'D-UPD-001',
        'villa_type_id' => $villaType->id,
        'customer_name' => 'Dashboard Customer',
        'is_sold' => true,
    ]);

    $response->assertRedirect(route('dashboard.villas.show', $villa));
    $this->assertDatabaseHas('villas', [
        'id' => $villa->id,
        'customer_name' => 'Dashboard Customer',
        'is_sold' => true,
    ]);
});

test('destroy soft deletes villa and redirects to index', function () {
    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'D-DEL-001', 'villa_type_id' => $villaType->id]);

    $response = $this->delete(route('dashboard.villas.destroy', $villa));

    $response->assertRedirect(route('dashboard.villas.index'));
    $this->assertSoftDeleted('villas', ['id' => $villa->id]);
});
