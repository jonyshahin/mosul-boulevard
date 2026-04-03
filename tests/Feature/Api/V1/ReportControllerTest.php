<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'FloorDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
    Artisan::call('db:seed', ['--class' => 'EngineerSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerUnitSeeder']);

    Sanctum::actingAs(User::factory()->create());
});

test('dashboard-stats returns correct structure', function () {
    $response = $this->getJson('/api/v1/reports/dashboard-stats');

    $response->assertOk()
        ->assertJsonStructure([
            'total_villas',
            'total_tower_units',
            'villas_sold',
            'tower_units_sold',
            'villas_sold_pct',
            'tower_units_sold_pct',
            'total_villa_tasks',
            'total_tower_tasks',
            'recent_villa_updates',
            'recent_tower_updates',
        ]);

    expect($response->json('total_villas'))->toBeGreaterThan(0)
        ->and($response->json('total_tower_units'))->toBeGreaterThan(0);
});

test('sales-summary returns villa and tower data', function () {
    $response = $this->getJson('/api/v1/reports/sales-summary');

    $response->assertOk()
        ->assertJsonStructure(['villas', 'towers']);

    expect($response->json('villas'))->toBeArray()
        ->and($response->json('towers'))->toBeArray();
});

test('structural-status returns data', function () {
    $response = $this->getJson('/api/v1/reports/structural-status');

    $response->assertOk()
        ->assertJsonStructure(['villas', 'towers']);
});

test('finishing-status returns data', function () {
    $response = $this->getJson('/api/v1/reports/finishing-status');

    $response->assertOk()
        ->assertJsonStructure(['villas', 'towers']);
});

test('facade-status returns data', function () {
    $response = $this->getJson('/api/v1/reports/facade-status');

    $response->assertOk()
        ->assertJsonStructure(['villas', 'towers']);
});
