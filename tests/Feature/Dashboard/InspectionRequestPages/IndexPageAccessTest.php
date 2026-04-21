<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

$inspectionPages = [
    'index' => ['/dashboard/inspection-requests', 'dashboard/inspection-requests/Index'],
    'create' => ['/dashboard/inspection-requests/create', 'dashboard/inspection-requests/Create'],
    'show' => ['/dashboard/inspection-requests/42', 'dashboard/inspection-requests/Show'],
    'edit' => ['/dashboard/inspection-requests/42/edit', 'dashboard/inspection-requests/Edit'],
];

$settingsPages = [
    'request-types' => ['/dashboard/settings/request-types', 'dashboard/settings/request-types/Index'],
    'notification-rules' => ['/dashboard/settings/notification-recipient-rules', 'dashboard/settings/notification-recipient-rules/Index'],
];

dataset('inspection_pages', $inspectionPages);
dataset('settings_pages', $settingsPages);
dataset('all_pages', array_merge($inspectionPages, $settingsPages));

test('admin sees inspection page', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('inspection_pages');

test('admin sees settings page', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('settings_pages');

test('engineer sees inspection page', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'engineer']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('inspection_pages');

test('engineer is forbidden on settings page', function (string $path) {
    $this->actingAs(User::factory()->create(['role' => 'engineer']));

    $this->get($path)->assertForbidden();
})->with('settings_pages');

test('viewer sees inspection page', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'viewer']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('inspection_pages');

test('viewer is forbidden on settings page', function (string $path) {
    $this->actingAs(User::factory()->create(['role' => 'viewer']));

    $this->get($path)->assertForbidden();
})->with('settings_pages');

test('customer is forbidden on every inspection/settings page', function (string $path) {
    $this->actingAs(User::factory()->create(['role' => 'customer']));

    $this->get($path)->assertForbidden();
})->with('all_pages');

test('guest is redirected to login on every page', function (string $path) {
    $this->get($path)->assertRedirect('/login');
})->with('all_pages');

test('inertia props carry translated title and coming_soon', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard/inspection-requests')
        ->assertInertia(fn (Assert $inertia) => $inertia
            ->component('dashboard/inspection-requests/Index')
            ->has('translations.title')
            ->has('translations.coming_soon')
        );
});

test('show page passes id prop', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard/inspection-requests/99')
        ->assertInertia(fn (Assert $inertia) => $inertia
            ->component('dashboard/inspection-requests/Show')
            ->where('id', 99)
        );
});

test('show route rejects non-numeric id', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard/inspection-requests/abc')->assertNotFound();
});
