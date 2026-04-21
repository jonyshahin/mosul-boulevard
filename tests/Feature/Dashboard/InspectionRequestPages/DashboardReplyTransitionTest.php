<?php

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\User;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
    $villa = InspectionRequestHelpers::makeVilla();
    $this->requester = InspectionRequestHelpers::engineer();
    $this->assignee = InspectionRequestHelpers::engineer();
    $this->request = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $this->requester->id,
        'assignee_id' => $this->assignee->id,
        'status' => RequestStatus::Open->value,
    ]);
});

test('assignee can post reply via dashboard route', function () {
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/replies", [
        'body' => 'Visited site, defect confirmed.',
    ])->assertRedirect();

    $this->assertDatabaseHas('request_replies', [
        'inspection_request_id' => $this->request->id,
        'body' => 'Visited site, defect confirmed.',
        'author_id' => $this->assignee->id,
    ]);
});

test('reply with triggers_status transitions the request', function () {
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/replies", [
        'body' => 'Starting work',
        'triggers_status' => RequestStatus::InProgress->value,
    ])->assertRedirect();

    expect($this->request->fresh()->status->value)->toBe('in_progress');
});

test('reply rejected on validation failure (empty body)', function () {
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/replies", [
        'body' => '',
    ])->assertSessionHasErrors(['body']);
});

test('non-assignee non-requester engineer cannot reply', function () {
    $outsider = InspectionRequestHelpers::engineer();
    $this->actingAs($outsider);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/replies", [
        'body' => 'Not allowed',
    ])->assertForbidden();
});

test('customer cannot reply', function () {
    $this->actingAs(User::factory()->create(['role' => 'customer']));

    $this->post("/dashboard/inspection-requests/{$this->request->id}/replies", [
        'body' => 'Denied',
    ])->assertForbidden();
});

test('assignee can transition open → in_progress via dashboard', function () {
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/transition", [
        'target_status' => RequestStatus::InProgress->value,
    ])->assertRedirect();

    expect($this->request->fresh()->status->value)->toBe('in_progress');
});

test('transition with note also creates a reply', function () {
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/transition", [
        'target_status' => RequestStatus::InProgress->value,
        'note' => 'Scheduled for tomorrow',
    ])->assertRedirect();

    $this->assertDatabaseHas('request_replies', [
        'inspection_request_id' => $this->request->id,
        'body' => 'Scheduled for tomorrow',
        'triggers_status' => 'in_progress',
    ]);
});

test('invalid transition returns 422', function () {
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/transition", [
        'target_status' => RequestStatus::Verified->value,
    ])->assertSessionHasErrors(['target_status']);
});

test('wrong actor for target returns 403', function () {
    // Only requester can verify; assignee cannot.
    $this->request->update(['status' => RequestStatus::Resolved->value]);
    $this->actingAs($this->assignee);

    $this->post("/dashboard/inspection-requests/{$this->request->id}/transition", [
        'target_status' => RequestStatus::Verified->value,
    ])->assertForbidden();
});

test('customer cannot transition', function () {
    $this->actingAs(User::factory()->create(['role' => 'customer']));

    $this->post("/dashboard/inspection-requests/{$this->request->id}/transition", [
        'target_status' => RequestStatus::InProgress->value,
    ])->assertForbidden();
});
