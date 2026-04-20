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

test('returns only auth user open and in_progress assignments', function () {
    $me = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($me);
    $other = InspectionRequestHelpers::engineer();
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $other->id, 'assignee_id' => $me->id,
        'status' => RequestStatus::Open->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $other->id, 'assignee_id' => $me->id,
        'status' => RequestStatus::InProgress->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $other->id, 'assignee_id' => $me->id,
        'status' => RequestStatus::Resolved->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $me->id, 'assignee_id' => $other->id,
        'status' => RequestStatus::Open->value,
    ]);

    $response = $this->getJson('/api/v1/inspection-requests/my-assignments');

    $response->assertOk();
    expect($response->json('meta.total'))->toBe(2);
});

test('customer receives 403', function () {
    Sanctum::actingAs(InspectionRequestHelpers::customer());

    $this->getJson('/api/v1/inspection-requests/my-assignments')->assertForbidden();
});
