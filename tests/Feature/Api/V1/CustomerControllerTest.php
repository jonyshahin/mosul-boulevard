<?php

use App\Models\Customer;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

test('index returns paginated customers', function () {
    Sanctum::actingAs(User::factory()->create());

    Customer::create(['name' => 'Alice']);
    Customer::create(['name' => 'Bob']);
    Customer::create(['name' => 'Charlie']);

    $response = $this->getJson('/api/v1/customers');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);

    expect($response->json('meta.total'))->toBe(3);
});

test('index without auth returns 401', function () {
    $this->getJson('/api/v1/customers')->assertUnauthorized();
});

test('index with search filter returns filtered results', function () {
    Sanctum::actingAs(User::factory()->create());

    Customer::create(['name' => 'Acme Corp', 'phone' => '555-0001']);
    Customer::create(['name' => 'Beta LLC', 'phone' => '555-0002']);

    $response = $this->getJson('/api/v1/customers?search=Acme');

    $response->assertOk();

    $names = collect($response->json('data'))->pluck('name');
    expect($names)->toContain('Acme Corp')
        ->and($names)->not->toContain('Beta LLC');
});

test('show returns customer with villas and tower units loaded', function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);

    Sanctum::actingAs(User::factory()->create());

    $customer = Customer::create(['name' => 'Linked Customer']);
    $villaType = VillaType::first();
    $tower = TowerDefinition::first();

    Villa::create([
        'code' => 'V-LINK-001',
        'villa_type_id' => $villaType->id,
        'customer_id' => $customer->id,
    ]);
    TowerUnit::create([
        'code' => 'TU-LINK-001',
        'tower_definition_id' => $tower->id,
        'customer_id' => $customer->id,
    ]);

    $response = $this->getJson("/api/v1/customers/{$customer->id}");

    $response->assertOk()
        ->assertJsonPath('data.name', 'Linked Customer')
        ->assertJsonStructure([
            'data' => ['id', 'name', 'villas', 'tower_units'],
        ]);

    expect($response->json('data.villas'))->toHaveCount(1)
        ->and($response->json('data.tower_units'))->toHaveCount(1);
});

test('store creates a new customer', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $response = $this->postJson('/api/v1/customers', [
        'name' => 'New Customer',
        'phone' => '+964 750 000 0000',
        'email' => 'new@example.com',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'New Customer');

    $this->assertDatabaseHas('customers', [
        'name' => 'New Customer',
        'email' => 'new@example.com',
    ]);
});

test('store validates required name field', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $response = $this->postJson('/api/v1/customers', [
        'phone' => '555-9999',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('update modifies customer data', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $customer = Customer::create(['name' => 'Old Name']);

    $response = $this->patchJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Updated Name',
        'phone' => '555-1234',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name',
        'phone' => '555-1234',
    ]);
});

test('destroy soft deletes customer', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $customer = Customer::create(['name' => 'Doomed']);

    $response = $this->deleteJson("/api/v1/customers/{$customer->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});
