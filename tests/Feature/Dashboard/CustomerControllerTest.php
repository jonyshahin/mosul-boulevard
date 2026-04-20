<?php

use App\Models\Customer;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

test('index page loads for authenticated user', function () {
    Customer::create(['name' => 'Sample Customer']);

    $response = $this->get(route('dashboard.customers.index'));

    $response->assertOk();
});

test('create page loads', function () {
    $response = $this->get(route('dashboard.customers.create'));

    $response->assertOk();
});

test('store creates customer and redirects to show', function () {
    $response = $this->post(route('dashboard.customers.store'), [
        'name' => 'Dashboard Customer',
        'phone' => '+964 750 111 2222',
        'email' => 'dash@example.com',
    ]);

    $customer = Customer::where('name', 'Dashboard Customer')->first();

    expect($customer)->not->toBeNull();
    $response->assertRedirect(route('dashboard.customers.show', $customer));
    $this->assertDatabaseHas('customers', [
        'name' => 'Dashboard Customer',
        'email' => 'dash@example.com',
    ]);
});

test('edit page loads with customer data', function () {
    $customer = Customer::create(['name' => 'Editable']);

    $response = $this->get(route('dashboard.customers.edit', $customer));

    $response->assertOk();
});

test('update modifies customer and redirects to show', function () {
    $customer = Customer::create(['name' => 'Old Name']);

    $response = $this->put(route('dashboard.customers.update', $customer), [
        'name' => 'New Name',
        'phone' => '555-1234',
    ]);

    $response->assertRedirect(route('dashboard.customers.show', $customer));
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'New Name',
        'phone' => '555-1234',
    ]);
});

test('destroy soft deletes customer and redirects to index', function () {
    $customer = Customer::create(['name' => 'Doomed']);

    $response = $this->delete(route('dashboard.customers.destroy', $customer));

    $response->assertRedirect(route('dashboard.customers.index'));
    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});
