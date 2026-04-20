<?php

use App\Models\RequestType;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('staff can list request types', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->getJson('/api/v1/request-types');

    $response->assertOk()->assertJsonStructure(['data', 'meta']);
    expect($response->json('meta.total'))->toBeGreaterThan(0);
});

test('customer rejected', function () {
    Sanctum::actingAs(InspectionRequestHelpers::customer());

    $this->getJson('/api/v1/request-types')->assertForbidden();
});

test('admin creates a request type', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $response = $this->postJson('/api/v1/request-types', [
        'name' => 'Custom Check',
        'category' => 'qaqc',
        'color' => '#123456',
        'is_active' => true,
        'sort_order' => 500,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('request_types', ['name' => 'Custom Check']);
});

test('engineer cannot create', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $this->postJson('/api/v1/request-types', [
        'name' => 'Nope',
        'category' => 'qaqc',
        'color' => '#000000',
    ])->assertForbidden();
});

test('admin updates an existing type', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $type = InspectionRequestHelpers::activeRequestType();

    $response = $this->patchJson("/api/v1/request-types/{$type->id}", [
        'color' => '#AABBCC',
    ]);

    $response->assertOk();
    expect($type->fresh()->color)->toBe('#AABBCC');
});

test('admin deletes a type', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $type = RequestType::create([
        'name' => 'ToDelete',
        'category' => 'other',
        'color' => '#000000',
    ]);

    $this->deleteJson("/api/v1/request-types/{$type->id}")->assertNoContent();
    $this->assertDatabaseMissing('request_types', ['id' => $type->id]);
});

test('store rejects invalid color format', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $this->postJson('/api/v1/request-types', [
        'name' => 'Bad color',
        'category' => 'qaqc',
        'color' => 'red',
    ])->assertUnprocessable()->assertJsonValidationErrors(['color']);
});

test('store rejects duplicate name', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $existing = InspectionRequestHelpers::activeRequestType();

    $this->postJson('/api/v1/request-types', [
        'name' => $existing->name,
        'category' => 'qaqc',
        'color' => '#123456',
    ])->assertUnprocessable()->assertJsonValidationErrors(['name']);
});
