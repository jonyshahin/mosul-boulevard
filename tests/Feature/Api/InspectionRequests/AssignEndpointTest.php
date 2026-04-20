<?php

use App\Models\InspectionRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('admin can reassign', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $engineer = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $response = $this->postJson("/api/v1/inspection-requests/{$req->id}/assign", [
        'assignee_id' => $engineer->id,
    ]);

    $response->assertOk();
    expect($req->fresh()->assignee_id)->toBe($engineer->id);
});

test('engineer cannot reassign', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/assign", [
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ])->assertForbidden();
});

test('customer assignee rejected as 422', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/assign", [
        'assignee_id' => InspectionRequestHelpers::customer()->id,
    ])->assertUnprocessable()->assertJsonValidationErrors(['assignee_id']);
});
