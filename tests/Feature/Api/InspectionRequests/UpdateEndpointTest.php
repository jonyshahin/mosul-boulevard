<?php

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('requester can update open request', function () {
    $requester = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($requester);
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $response = $this->patchJson("/api/v1/inspection-requests/{$req->id}", [
        'title' => 'Refined title',
    ]);

    $response->assertOk()->assertJsonPath('data.title', 'Refined title');
});

test('update blocked after resolved', function () {
    $requester = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($requester);
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'status' => RequestStatus::Resolved->value,
    ]);

    $response = $this->patchJson("/api/v1/inspection-requests/{$req->id}", ['title' => 'Nope']);

    $response->assertForbidden();
});

test('admin can update regardless of requester', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $response = $this->patchJson("/api/v1/inspection-requests/{$req->id}", ['title' => 'Admin override']);

    $response->assertOk();
});

test('non-requester engineer cannot update', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->patchJson("/api/v1/inspection-requests/{$req->id}", ['title' => 'Nope'])
        ->assertForbidden();
});
