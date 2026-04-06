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
