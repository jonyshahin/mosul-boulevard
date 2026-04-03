<?php

use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'FloorDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
    Artisan::call('db:seed', ['--class' => 'EngineerSeeder']);
});

test('index returns paginated tower units', function () {
    Sanctum::actingAs(User::factory()->create());
    Artisan::call('db:seed', ['--class' => 'TowerUnitSeeder']);

    $response = $this->getJson('/api/v1/tower-units');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);

    expect($response->json('meta.total'))->toBeGreaterThan(0);
});

test('index without auth returns 401', function () {
    $this->getJson('/api/v1/tower-units')->assertUnauthorized();
});

test('index with search filter returns filtered results', function () {
    Sanctum::actingAs(User::factory()->create());

    $tower = TowerDefinition::first();
    TowerUnit::create(['code' => 'TU-TEST-001', 'tower_definition_id' => $tower->id]);
    TowerUnit::create(['code' => 'TU-OTHER-002', 'tower_definition_id' => $tower->id]);

    $response = $this->getJson('/api/v1/tower-units?search=TEST');

    $response->assertOk();

    $codes = collect($response->json('data'))->pluck('code');
    expect($codes)->toContain('TU-TEST-001')
        ->and($codes)->not->toContain('TU-OTHER-002');
});

test('index with tower_definition_id filter works', function () {
    Sanctum::actingAs(User::factory()->create());
    Artisan::call('db:seed', ['--class' => 'TowerUnitSeeder']);

    $tower = TowerDefinition::first();

    $response = $this->getJson("/api/v1/tower-units?tower_definition_id={$tower->id}");

    $response->assertOk();

    collect($response->json('data'))->each(function ($unit) use ($tower) {
        expect($unit['tower_definition']['id'])->toBe($tower->id);
    });
});

test('show returns tower unit with relationships', function () {
    Sanctum::actingAs(User::factory()->create());

    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'TU-SHOW-001', 'tower_definition_id' => $tower->id]);

    $response = $this->getJson("/api/v1/tower-units/{$unit->id}");

    $response->assertOk()
        ->assertJsonPath('data.code', 'TU-SHOW-001')
        ->assertJsonStructure(['data' => ['id', 'code', 'tower_definition']]);
});

test('store creates a new tower unit', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $tower = TowerDefinition::first();

    $response = $this->postJson('/api/v1/tower-units', [
        'code' => 'TU-NEW-001',
        'tower_definition_id' => $tower->id,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.code', 'TU-NEW-001');

    $this->assertDatabaseHas('tower_units', ['code' => 'TU-NEW-001']);
});

test('update modifies tower unit data', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'TU-UPD-001', 'tower_definition_id' => $tower->id]);

    $response = $this->patchJson("/api/v1/tower-units/{$unit->id}", [
        'tower_definition_id' => $tower->id,
        'customer_name' => 'Tower Customer',
        'is_sold' => true,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.customer_name', 'Tower Customer');

    $this->assertDatabaseHas('tower_units', [
        'id' => $unit->id,
        'customer_name' => 'Tower Customer',
    ]);
});

test('destroy soft deletes tower unit', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'TU-DEL-001', 'tower_definition_id' => $tower->id]);

    $response = $this->deleteJson("/api/v1/tower-units/{$unit->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('tower_units', ['id' => $unit->id]);
});
