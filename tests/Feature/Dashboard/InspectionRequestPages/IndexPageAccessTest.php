<?php

use App\Models\InspectionRequest;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
    $villa = InspectionRequestHelpers::makeVilla();
    $this->request = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);
});

$listAndCreate = [
    'index' => ['/dashboard/inspection-requests', 'dashboard/inspection-requests/Index'],
    'create' => ['/dashboard/inspection-requests/create', 'dashboard/inspection-requests/Create'],
];

$settingsPages = [
    'request-types' => ['/dashboard/settings/request-types', 'dashboard/settings/request-types/Index'],
    'notification-rules' => ['/dashboard/settings/notification-recipient-rules', 'dashboard/settings/notification-recipient-rules/Index'],
];

dataset('list_create_pages', $listAndCreate);
dataset('settings_pages', $settingsPages);
dataset('all_static_pages', array_merge($listAndCreate, $settingsPages));

test('admin sees list and create pages', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('list_create_pages');

test('admin sees settings page', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('settings_pages');

test('admin sees inspection show page for real request', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia
            ->component('dashboard/inspection-requests/Show')
            ->where('request.id', $this->request->id)
        );
});

test('admin sees inspection edit page (still placeholder)', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component('dashboard/inspection-requests/Edit'));
});

test('engineer sees list and create pages', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'engineer']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('list_create_pages');

test('engineer is forbidden on settings page', function (string $path) {
    $this->actingAs(User::factory()->create(['role' => 'engineer']));

    $this->get($path)->assertForbidden();
})->with('settings_pages');

test('viewer sees list and create pages', function (string $path, string $component) {
    $this->actingAs(User::factory()->create(['role' => 'viewer']));

    $this->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $inertia) => $inertia->component($component));
})->with('list_create_pages');

test('viewer is forbidden on settings page', function (string $path) {
    $this->actingAs(User::factory()->create(['role' => 'viewer']));

    $this->get($path)->assertForbidden();
})->with('settings_pages');

test('customer is forbidden on every inspection/settings page', function (string $path) {
    $this->actingAs(User::factory()->create(['role' => 'customer']));

    $this->get($path)->assertForbidden();
})->with('all_static_pages');

test('customer is forbidden on show', function () {
    $this->actingAs(User::factory()->create(['role' => 'customer']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}")->assertForbidden();
});

test('guest is redirected to login on every page', function (string $path) {
    $this->get($path)->assertRedirect('/login');
})->with('all_static_pages');

test('inertia props include list translations and shared translations', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard/inspection-requests')
        ->assertInertia(fn (Assert $inertia) => $inertia
            ->component('dashboard/inspection-requests/Index')
            ->has('translations.title')
            ->has('translations.list.filters.status')
            ->has('translations.list.columns.id')
            ->has('translations.shared.severity.critical')
            ->has('translations.shared.status.open')
            ->has('requests.data')
            ->has('filters.sort')
        );
});

test('show route rejects non-numeric id', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard/inspection-requests/abc')->assertNotFound();
});

test('show route returns 404 for unknown numeric id', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard/inspection-requests/99999')->assertNotFound();
});
