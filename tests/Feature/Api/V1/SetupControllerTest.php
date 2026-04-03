<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
    Artisan::call('db:seed', ['--class' => 'EngineerSeeder']);

    Sanctum::actingAs(User::factory()->create());
});

test('stages returns construction stages', function () {
    $response = $this->getJson('/api/v1/setup/stages');

    $response->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name']]]);

    expect($response->json('data'))->not->toBeEmpty();
});

test('stages with property_type filter returns filtered results', function () {
    $response = $this->getJson('/api/v1/setup/stages?property_type=villas');

    $response->assertOk();

    expect($response->json('data'))->not->toBeEmpty();
});

test('statuses returns status options', function () {
    $response = $this->getJson('/api/v1/setup/statuses');

    $response->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name']]]);

    expect($response->json('data'))->not->toBeEmpty();
});

test('statuses with category filter returns filtered results', function () {
    $response = $this->getJson('/api/v1/setup/statuses?category=unit');

    $response->assertOk();

    expect($response->json('data'))->not->toBeEmpty();
});

test('engineers returns active engineers', function () {
    $response = $this->getJson('/api/v1/setup/engineers');

    $response->assertOk()
        ->assertJsonStructure(['data']);

    expect($response->json('data'))->not->toBeEmpty();
});
