<?php

use App\Models\ContactMessage;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
    Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'FloorDefinitionSeeder']);
    Artisan::call('db:seed', ['--class' => 'StatusOptionSeeder']);
    Artisan::call('db:seed', ['--class' => 'ConstructionStageSeeder']);
});

test('home page loads', function () {
    $response = $this->get(route('website.home'));

    $response->assertOk();
});

test('villas index page loads', function () {
    $response = $this->get(route('website.villas.index'));

    $response->assertOk();
});

test('villas show page loads', function () {
    $villaType = VillaType::first();
    $villa = Villa::create(['code' => 'WEB-V-001', 'villa_type_id' => $villaType->id]);

    $response = $this->get(route('website.villas.show', $villa));

    $response->assertOk();
});

test('towers index page loads', function () {
    $response = $this->get(route('website.towers.index'));

    $response->assertOk();
});

test('towers show page loads', function () {
    $tower = TowerDefinition::first();
    $unit = TowerUnit::create(['code' => 'WEB-TU-001', 'tower_definition_id' => $tower->id]);

    $response = $this->get(route('website.towers.show', $unit));

    $response->assertOk();
});

test('progress page loads', function () {
    $response = $this->get(route('website.progress'));

    $response->assertOk();
});

test('contact page loads', function () {
    Artisan::call('db:seed', ['--class' => 'SettingSeeder']);

    $response = $this->get(route('website.contact'));

    $response->assertOk();
});

test('contact form submission creates message', function () {
    $response = $this->post(route('website.contact.store'), [
        'name' => 'John Visitor',
        'email' => 'visitor@example.com',
        'subject' => 'Project inquiry',
        'message' => 'Could you tell me more about the project?',
    ]);

    $response->assertRedirect(route('website.contact'));

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Visitor',
        'email' => 'visitor@example.com',
        'subject' => 'Project inquiry',
    ]);

    expect(ContactMessage::count())->toBe(1);
});

test('contact form validates required fields', function () {
    $response = $this->post(route('website.contact.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
});
