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

test('assignee can transition open → in_progress', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Open->value,
    ]);

    $response = $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::InProgress->value,
    ]);

    $response->assertOk()->assertJsonPath('data.status.value', 'in_progress');
});

test('assignee can resolve and resolved_at is set', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::InProgress->value,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::Resolved->value,
    ])->assertOk();

    expect($req->fresh()->resolved_at)->not->toBeNull();
});

test('requester can verify and verified_at + verified_by are set', function () {
    $requester = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($requester);
    $assignee = InspectionRequestHelpers::engineer();

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Resolved->value,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::Verified->value,
    ])->assertOk();

    $fresh = $req->fresh();
    expect($fresh->verified_at)->not->toBeNull()
        ->and($fresh->verified_by)->toBe($requester->id);
});

test('requester can close and closed_at is set', function () {
    $requester = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($requester);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'status' => RequestStatus::Verified->value,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::Closed->value,
    ])->assertOk();

    expect($req->fresh()->closed_at)->not->toBeNull();
});

test('invalid transition returns 422', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Open->value,
    ]);

    $response = $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::Verified->value,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['target_status']);
});

test('wrong actor for verified returns 403', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Resolved->value,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::Verified->value,
    ])->assertForbidden();
});

test('note creates a reply', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Open->value,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::InProgress->value,
        'note' => 'Starting inspection now.',
    ])->assertOk();

    $this->assertDatabaseHas('request_replies', [
        'inspection_request_id' => $req->id,
        'body' => 'Starting inspection now.',
        'triggers_status' => 'in_progress',
    ]);
});

test('reopened clears verified and closed timestamps', function () {
    $requester = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($requester);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'status' => RequestStatus::Closed->value,
        'resolved_at' => now()->subDays(2),
        'verified_at' => now()->subDays(1),
        'closed_at' => now(),
        'verified_by' => $requester->id,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/transition", [
        'target_status' => RequestStatus::Reopened->value,
    ])->assertOk();

    $fresh = $req->fresh();
    expect($fresh->resolved_at)->toBeNull()
        ->and($fresh->verified_at)->toBeNull()
        ->and($fresh->closed_at)->toBeNull()
        ->and($fresh->verified_by)->toBeNull();
});
