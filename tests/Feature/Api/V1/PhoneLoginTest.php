<?php

use App\Models\Customer;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Support\Facades\Artisan;

test('phone login with valid phone returns 200 with customer data and token', function () {
    $customer = Customer::create([
        'name' => 'Mobile Customer',
        'phone' => '+9647501234567',
    ]);

    $response = $this->postJson('/api/v1/auth/phone-login', [
        'phone' => '+9647501234567',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'role'],
            'customer' => ['id', 'name', 'phone', 'villas', 'tower_units'],
            'token',
        ])
        ->assertJsonPath('customer.id', $customer->id)
        ->assertJsonPath('customer.name', 'Mobile Customer')
        ->assertJsonPath('user.role', 'customer');

    expect($response->json('token'))->toBeString()->not->toBeEmpty();

    // A user record was created and linked to the customer.
    $this->assertDatabaseHas('users', [
        'customer_id' => $customer->id,
        'role' => 'customer',
    ]);
});

test('phone login normalizes phone number variants', function () {
    // Stored in international format, queried with local format.
    $customer = Customer::create([
        'name' => 'Variant Customer',
        'phone' => '+9647509998888',
    ]);

    $response = $this->postJson('/api/v1/auth/phone-login', [
        'phone' => '07509998888',
    ]);

    $response->assertOk()
        ->assertJsonPath('customer.id', $customer->id);
});

test('phone login reuses existing user on subsequent calls', function () {
    $customer = Customer::create([
        'name' => 'Repeat Customer',
        'phone' => '+9647501112222',
    ]);

    $this->postJson('/api/v1/auth/phone-login', ['phone' => '+9647501112222'])
        ->assertOk();
    $this->postJson('/api/v1/auth/phone-login', ['phone' => '+9647501112222'])
        ->assertOk();

    expect(User::where('customer_id', $customer->id)->count())->toBe(1);
});

test('phone login with unknown phone returns 404', function () {
    $response = $this->postJson('/api/v1/auth/phone-login', [
        'phone' => '+9647500000000',
    ]);

    $response->assertNotFound()
        ->assertJsonPath('message', 'No customer found with this phone number');
});

test('phone login validates required phone field', function () {
    $response = $this->postJson('/api/v1/auth/phone-login', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['phone']);
});

test('phone login loads customers villas and tower units', function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);

    $customer = Customer::create([
        'name' => 'Property Owner',
        'phone' => '+9647505556666',
    ]);

    $villaType = VillaType::first();
    $tower = TowerDefinition::first();

    Villa::create([
        'code' => 'PHONE-V-001',
        'villa_type_id' => $villaType->id,
        'customer_id' => $customer->id,
    ]);
    Villa::create([
        'code' => 'PHONE-V-002',
        'villa_type_id' => $villaType->id,
        'customer_id' => $customer->id,
    ]);
    TowerUnit::create([
        'code' => 'PHONE-TU-001',
        'tower_definition_id' => $tower->id,
        'customer_id' => $customer->id,
    ]);

    $response = $this->postJson('/api/v1/auth/phone-login', [
        'phone' => '+9647505556666',
    ]);

    $response->assertOk();

    expect($response->json('customer.villas'))->toHaveCount(2)
        ->and($response->json('customer.tower_units'))->toHaveCount(1);

    $villaCodes = collect($response->json('customer.villas'))->pluck('code');
    expect($villaCodes)->toContain('PHONE-V-001')
        ->and($villaCodes)->toContain('PHONE-V-002');
});
