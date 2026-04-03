<?php

use App\Models\User;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
    Artisan::call('db:seed', ['--class' => 'EngineerSeeder']);
});

test('index returns paginated villas', function () {
    Sanctum::actingAs(User::factory()->create());
    Artisan::call('db:seed', ['--class' => 'VillaSeeder']);

    $response = $this->getJson('/api/v1/villas');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);

    expect($response->json('data'))->toBeArray()
        ->and($response->json('meta.total'))->toBeGreaterThan(0);
});

test('index without auth returns 401', function () {
    $this->getJson('/api/v1/villas')->assertUnauthorized();
});

test('index with search filter returns filtered results', function () {
    Sanctum::actingAs(User::factory()->create());

    $villaType = VillaType::first();
    Villa::create(['code' => 'TEST-001', 'villa_type_id' => $villaType->id]);
    Villa::create(['code' => 'OTHER-002', 'villa_type_id' => $villaType->id]);

    $response = $this->getJson('/api/v1/villas?search=TEST');

    $response->assertOk();

    $codes = collect($response->json('data'))->pluck('code');
    expect($codes)->toContain('TEST-001')
        ->and($codes)->not->toContain('OTHER-002');
});

test('index with villa_type_id filter works', function () {
    Sanctum::actingAs(User::factory()->create());
    Artisan::call('db:seed', ['--class' => 'VillaSeeder']);

    $villaType = VillaType::first();

    $response = $this->getJson("/api/v1/villas?villa_type_id={$villaType->id}");

    $response->assertOk();

    collect($response->json('data'))->each(function ($villa) use ($villaType) {
        expect($villa['villa_type']['id'])->toBe($villaType->id);
    });
});

test('show returns villa with relationships', function () {
    Sanctum::actingAs(User::factory()->create());

    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'SHOW-001', 'villa_type_id' => $villaType->id]);

    $response = $this->getJson("/api/v1/villas/{$villa->id}");

    $response->assertOk()
        ->assertJsonPath('data.code', 'SHOW-001')
        ->assertJsonStructure(['data' => ['id', 'code', 'villa_type']]);
});

test('store creates a new villa', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $villaType = VillaType::first();

    $response = $this->postJson('/api/v1/villas', [
        'code' => 'NEW-001',
        'villa_type_id' => $villaType->id,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.code', 'NEW-001');

    $this->assertDatabaseHas('villas', ['code' => 'NEW-001']);
});

test('update modifies villa data', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'UPD-001', 'villa_type_id' => $villaType->id]);

    $response = $this->patchJson("/api/v1/villas/{$villa->id}", [
        'villa_type_id' => $villaType->id,
        'customer_name' => 'Updated Customer',
        'is_sold' => true,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.customer_name', 'Updated Customer');

    $this->assertDatabaseHas('villas', [
        'id' => $villa->id,
        'customer_name' => 'Updated Customer',
        'is_sold' => true,
    ]);
});

test('destroy soft deletes villa', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'DEL-001', 'villa_type_id' => $villaType->id]);

    $response = $this->deleteJson("/api/v1/villas/{$villa->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('villas', ['id' => $villa->id]);
});
