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
