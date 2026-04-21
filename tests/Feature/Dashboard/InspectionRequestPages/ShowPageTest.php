<?php

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
    $villa = InspectionRequestHelpers::makeVilla();
    $this->requester = InspectionRequestHelpers::engineer();
    $this->assignee = InspectionRequestHelpers::engineer();
    $this->request = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $this->requester->id,
        'assignee_id' => $this->assignee->id,
    ]);
});

test('show page returns detail resource with title, severity, status', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $i) => $i
            ->component('dashboard/inspection-requests/Show')
            ->where('request.id', $this->request->id)
            ->where('request.title', $this->request->title)
            ->has('request.severity.value')
            ->has('request.severity.label')
            ->has('request.severity.color')
            ->has('request.status.value')
            ->has('request.description')
        );
});

test('show page includes subject polymorphic data with villa code', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertInertia(fn (Assert $i) => $i
            ->has('request.subject.type')
            ->has('request.subject.id')
            ->where('request.subject.type', 'villa')
        );
});

test('show page includes replies with author and triggers_status shape', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    RequestReply::factory()->for($this->request, 'request')->create([
        'author_id' => $this->assignee->id,
        'body' => 'In progress on site',
        'triggers_status' => RequestStatus::InProgress->value,
    ]);

    $response = $this->get("/dashboard/inspection-requests/{$this->request->id}");
    $response->assertOk();

    $props = $response->viewData('page')['props'];
    $reply = $props['request']['replies'][0];

    expect($reply['body'])->toBe('In progress on site');
    expect($reply['author'])->not->toBeNull();
    expect($reply['author']['id'])->toBe($this->assignee->id);
    expect($reply['triggers_status']['value'])->toBe('in_progress');
    expect($reply['triggers_status']['label'])->toBe('In Progress');
});

test('show page includes media count placeholder', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertInertia(fn (Assert $i) => $i->has('request.media_count'));
});

test('show page auth prop lists allowed transitions from current state', function () {
    $this->actingAs($this->assignee);

    // Request is Open by default. Open → [in_progress, resolved, closed].
    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertInertia(fn (Assert $i) => $i
            ->has('auth.can_transition_to')
            ->where('auth.can_transition_to.in_progress', true)
            ->where('auth.can_transition_to.resolved', true)
            ->where('auth.can_transition_to.closed', false) // assignee can open→resolved; only requester can close
        );
});

test('show page auth.can_reply is true for assignee', function () {
    $this->actingAs($this->assignee);

    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertInertia(fn (Assert $i) => $i->where('auth.can_reply', true));
});

test('show page auth.can_reply is false for unrelated engineer', function () {
    $outsider = InspectionRequestHelpers::engineer();
    $this->actingAs($outsider);

    $this->get("/dashboard/inspection-requests/{$this->request->id}")
        ->assertInertia(fn (Assert $i) => $i->where('auth.can_reply', false));
});

test('show page forbidden for customer', function () {
    $this->actingAs(User::factory()->create(['role' => 'customer']));

    $this->get("/dashboard/inspection-requests/{$this->request->id}")->assertForbidden();
});
