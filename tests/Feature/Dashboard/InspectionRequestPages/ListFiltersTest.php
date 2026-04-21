<?php

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('filter by status returns only matching requests', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'status' => RequestStatus::Open->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'status' => RequestStatus::Resolved->value,
    ]);

    $this->get('/dashboard/inspection-requests?status[]=open')
        ->assertOk()
        ->assertInertia(fn (Assert $i) => $i
            ->component('dashboard/inspection-requests/Index')
            ->where('requests.meta.total', 1)
            ->where('filters.status', ['open'])
        );
});

test('filter by multiple statuses via status[] array', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'status' => RequestStatus::Open->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'status' => RequestStatus::InProgress->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'status' => RequestStatus::Closed->value]);

    $this->get('/dashboard/inspection-requests?status[]=open&status[]=in_progress')
        ->assertOk()
        ->assertInertia(fn (Assert $i) => $i->where('requests.meta.total', 2));
});

test('filter by severity', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::Critical->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::Low->value]);

    $this->get('/dashboard/inspection-requests?severity[]=critical')
        ->assertInertia(fn (Assert $i) => $i->where('requests.meta.total', 1));
});

test('filter by subject_type=villa excludes tower units', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $unit = InspectionRequestHelpers::makeTowerUnit();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);
    InspectionRequest::factory()->forTowerUnit($unit)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);

    $this->get('/dashboard/inspection-requests?subject_type=villa')
        ->assertInertia(fn (Assert $i) => $i->where('requests.meta.total', 1));
});

test('filter by subject_type=tower_unit excludes villas', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $unit = InspectionRequestHelpers::makeTowerUnit();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);
    InspectionRequest::factory()->forTowerUnit($unit)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);

    $this->get('/dashboard/inspection-requests?subject_type=tower_unit')
        ->assertInertia(fn (Assert $i) => $i->where('requests.meta.total', 1));
});

test('assigned_to_me scopes to current user', function () {
    $me = InspectionRequestHelpers::engineer();
    $other = InspectionRequestHelpers::engineer();
    $this->actingAs($me);
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $me->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $me->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $other->id]);

    $this->get('/dashboard/inspection-requests?assigned_to_me=1')
        ->assertInertia(fn (Assert $i) => $i
            ->where('requests.meta.total', 2)
            ->where('filters.assigned_to_me', true)
        );
});

test('engineer default view is assigned_to_me on first visit with no query', function () {
    $me = InspectionRequestHelpers::engineer();
    $other = InspectionRequestHelpers::engineer();
    $this->actingAs($me);
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $me->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $other->id]);

    $this->get('/dashboard/inspection-requests')
        ->assertInertia(fn (Assert $i) => $i
            ->where('filters.assigned_to_me', true)
            ->where('requests.meta.total', 1)
        );
});

test('admin default view is NOT assigned_to_me', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $other = InspectionRequestHelpers::engineer();
    $this->actingAs($admin);
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->count(3)->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $other->id]);

    $this->get('/dashboard/inspection-requests')
        ->assertInertia(fn (Assert $i) => $i
            ->where('filters.assigned_to_me', false)
            ->where('requests.meta.total', 3)
        );
});

test('assigned_to_me=0 override allows engineer to see all', function () {
    $me = InspectionRequestHelpers::engineer();
    $other = InspectionRequestHelpers::engineer();
    $this->actingAs($me);
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $me->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $other->id, 'assignee_id' => $other->id]);

    $this->get('/dashboard/inspection-requests?assigned_to_me=0')
        ->assertInertia(fn (Assert $i) => $i
            ->where('filters.assigned_to_me', false)
            ->where('requests.meta.total', 2)
        );
});

test('overdue_only filter excludes verified/closed', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Open->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Verified->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->addDays(5),
        'status' => RequestStatus::Open->value,
    ]);

    $this->get('/dashboard/inspection-requests?overdue_only=1')
        ->assertInertia(fn (Assert $i) => $i->where('requests.meta.total', 1));
});

test('sort=-severity orders critical first', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::Low->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::Critical->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::Medium->value]);

    $this->get('/dashboard/inspection-requests?sort=-severity')
        ->assertInertia(fn (Assert $i) => $i
            ->where('requests.data.0.severity.value', 'critical')
            ->where('requests.data.1.severity.value', 'medium')
            ->where('requests.data.2.severity.value', 'low')
        );
});

test('sort=due_date puts nulls last and earliest dates first', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'due_date' => null]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'due_date' => now()->addDays(10)->toDateString()]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'due_date' => now()->addDays(2)->toDateString()]);

    $this->get('/dashboard/inspection-requests?sort=due_date')
        ->assertInertia(fn (Assert $i) => $i
            ->where('requests.data.0.due_date', fn ($v) => $v !== null)
            ->where('requests.data.2.due_date', null)
        );
});

test('pagination works — page 2 returns next slice', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->count(25)->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);

    $this->get('/dashboard/inspection-requests?page=2')
        ->assertInertia(fn (Assert $i) => $i
            ->where('requests.meta.current_page', 2)
            ->where('requests.meta.per_page', 20)
            ->where('requests.meta.total', 25)
        );
});
