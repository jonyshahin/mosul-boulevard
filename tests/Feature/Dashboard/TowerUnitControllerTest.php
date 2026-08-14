<?php

use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'FloorDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
    Artisan::call('db:seed', ['--class' => 'EngineerSeeder']);

    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

test('index page loads for authenticated user', function () {
    $response = $this->get(route('dashboard.tower-units.index'));

    $response->assertOk();
});

test('create page loads for authenticated user', function () {
    $response = $this->get(route('dashboard.tower-units.create'));

    $response->assertOk();
});

test('store creates tower unit and redirects to show', function () {
    $tower = TowerDefinition::first();

    $response = $this->post(route('dashboard.tower-units.store'), [
        'code' => 'D-TU-NEW-001',
        'tower_definition_id' => $tower->id,
    ]);

    $unit = TowerUnit::where('code', 'D-TU-NEW-001')->first();

    expect($unit)->not->toBeNull();
    $response->assertRedirect(route('dashboard.tower-units.show', $unit));
    $this->assertDatabaseHas('tower_units', ['code' => 'D-TU-NEW-001']);
});

/**
 * Mirrors preparePayload() in resources/js/pages/dashboard/tower-units/Create.tsx:
 * every optional field is sent as an explicit null when left blank.
 *
 * @return array<string, mixed>
 */
function blankTowerUnitFormPayload(string $code, int $towerId): array
{
    return [
        'code' => $code,
        'tower_definition_id' => $towerId,
        'floor_definition_id' => null,
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
        'remarks' => null,
    ];
}

test('store accepts the form payload with every optional field left blank', function () {
    $tower = TowerDefinition::first();

    $response = $this->post(
        route('dashboard.tower-units.store'),
        blankTowerUnitFormPayload('D-TU-BLANK-001', $tower->id)
    );

    $unit = TowerUnit::where('code', 'D-TU-BLANK-001')->first();

    expect($unit)->not->toBeNull();
    $response->assertRedirect(route('dashboard.tower-units.show', $unit));

    // completion_pct / acc_concrete_qty / acc_steel_qty are NOT NULL DEFAULT 0,
    // so a blank input has to land as 0 rather than null.
    expect($unit->completion_pct)->toBe(0.0)
        ->and($unit->acc_concrete_qty)->toBe(0.0)
        ->and($unit->acc_steel_qty)->toBe(0.0);
});

test('update accepts the form payload with every optional field left blank', function () {
    $tower = TowerDefinition::first();
    $unit = TowerUnit::create([
        'code' => 'D-TU-BLANK-002',
        'tower_definition_id' => $tower->id,
        'completion_pct' => 42,
        'acc_concrete_qty' => 10,
        'acc_steel_qty' => 5,
    ]);

    $response = $this->put(
        route('dashboard.tower-units.update', $unit),
        blankTowerUnitFormPayload('D-TU-BLANK-002', $tower->id)
    );

    $response->assertRedirect(route('dashboard.tower-units.show', $unit));

    expect($unit->fresh()->completion_pct)->toBe(0.0);
});

test('store leaves progress fields at their database default when omitted', function () {
    $tower = TowerDefinition::first();

    $this->post(route('dashboard.tower-units.store'), [
        'code' => 'D-TU-OMIT-001',
        'tower_definition_id' => $tower->id,
    ]);

    $unit = TowerUnit::where('code', 'D-TU-OMIT-001')->first();

    expect($unit->completion_pct)->toBe(0.0);
});

test('edit page loads with tower unit data', function () {
    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'D-TU-EDIT-001', 'tower_definition_id' => $tower->id]);

    $response = $this->get(route('dashboard.tower-units.edit', $unit));

    $response->assertOk();
});

test('update modifies tower unit and redirects to show', function () {
    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'D-TU-UPD-001', 'tower_definition_id' => $tower->id]);

    $response = $this->put(route('dashboard.tower-units.update', $unit), [
        'code' => 'D-TU-UPD-001',
        'tower_definition_id' => $tower->id,
        'customer_name' => 'Dashboard Tower Customer',
        'is_sold' => true,
    ]);

    $response->assertRedirect(route('dashboard.tower-units.show', $unit));
    $this->assertDatabaseHas('tower_units', [
        'id' => $unit->id,
        'customer_name' => 'Dashboard Tower Customer',
        'is_sold' => true,
    ]);
});

test('destroy soft deletes tower unit and redirects to index', function () {
    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'D-TU-DEL-001', 'tower_definition_id' => $tower->id]);

    $response = $this->delete(route('dashboard.tower-units.destroy', $unit));

    $response->assertRedirect(route('dashboard.tower-units.index'));
    $this->assertSoftDeleted('tower_units', ['id' => $unit->id]);
});
