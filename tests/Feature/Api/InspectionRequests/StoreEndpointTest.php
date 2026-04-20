<?php

use App\Enums\RequestSeverity;
use App\Models\InspectionRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

function ir_store_payload(array $overrides = []): array
{
    $villa = InspectionRequestHelpers::makeVilla();

    return array_merge([
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'subject_type' => 'villa',
        'subject_id' => $villa->id,
        'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
        'title' => 'Concrete cover defect',
        'description' => 'North wall — insufficient cover.',
        'severity' => RequestSeverity::Medium->value,
    ], $overrides);
}

test('engineer can create without media', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->postJson('/api/v1/inspection-requests', ir_store_payload());

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Concrete cover defect');

    $this->assertDatabaseCount('inspection_requests', 1);
});

test('admin can create', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $this->postJson('/api/v1/inspection-requests', ir_store_payload())->assertCreated();
});

test('viewer cannot create', function () {
    Sanctum::actingAs(InspectionRequestHelpers::viewer());

    $this->postJson('/api/v1/inspection-requests', ir_store_payload())->assertForbidden();
});

test('customer cannot create', function () {
    Sanctum::actingAs(InspectionRequestHelpers::customer());

    $this->postJson('/api/v1/inspection-requests', ir_store_payload())->assertForbidden();
});

test('creates with image media', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $payload = ir_store_payload([
        'media' => [UploadedFile::fake()->image('defect.jpg', 800, 600)],
    ]);

    $response = $this->postJson('/api/v1/inspection-requests', $payload);

    $response->assertCreated();

    $id = $response->json('data.id');
    $request = InspectionRequest::with('media')->find($id);

    expect($request->media)->toHaveCount(1)
        ->and($request->media->first()->media_type->value)->toBe('image');
});

test('creates with video media', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $payload = ir_store_payload([
        'media' => [UploadedFile::fake()->create('clip.mp4', 2000, 'video/mp4')],
    ]);

    $this->postJson('/api/v1/inspection-requests', $payload)->assertCreated();
});

test('rejects when assignee is a customer', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->postJson(
        '/api/v1/inspection-requests',
        ir_store_payload(['assignee_id' => InspectionRequestHelpers::customer()->id]),
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['assignee_id']);
});

test('rejects invalid severity', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->postJson(
        '/api/v1/inspection-requests',
        ir_store_payload(['severity' => 'bogus']),
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['severity']);
});

test('rejects due_date in the past', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->postJson(
        '/api/v1/inspection-requests',
        ir_store_payload(['due_date' => now()->subDay()->toDateString()]),
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['due_date']);
});

test('rejects unknown subject_id', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->postJson(
        '/api/v1/inspection-requests',
        ir_store_payload(['subject_type' => 'villa', 'subject_id' => 99999]),
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['subject_id']);
});

test('unauthenticated 401', function () {
    $this->postJson('/api/v1/inspection-requests', ir_store_payload())->assertUnauthorized();
});
