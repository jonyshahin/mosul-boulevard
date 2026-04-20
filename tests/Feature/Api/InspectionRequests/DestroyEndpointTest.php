<?php

use App\Models\InspectionRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('admin can soft-delete', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->deleteJson("/api/v1/inspection-requests/{$req->id}")->assertNoContent();

    $this->assertSoftDeleted('inspection_requests', ['id' => $req->id]);
});

test('engineer cannot delete', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->deleteJson("/api/v1/inspection-requests/{$req->id}")->assertForbidden();

    $this->assertDatabaseHas('inspection_requests', ['id' => $req->id, 'deleted_at' => null]);
});
