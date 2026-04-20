<?php

use App\Enums\RequestStatus;
use App\Events\InspectionRequestAssigned;
use App\Events\InspectionRequestCreated;
use App\Events\InspectionRequestTransitioned;
use App\Events\RequestReplyCreated;
use App\Models\InspectionRequest;
use App\Services\InspectionRequestService;
use App\Services\RequestReplyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('create dispatches InspectionRequestCreated', function () {
    Event::fake();
    $author = InspectionRequestHelpers::engineer();
    $villa = InspectionRequestHelpers::makeVilla();

    app(InspectionRequestService::class)->create([
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'subject_type' => 'villa',
        'subject_id' => $villa->id,
        'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
        'title' => 'T',
        'description' => 'D',
        'severity' => 'medium',
    ], $author);

    Event::assertDispatched(InspectionRequestCreated::class);
});

test('transition dispatches InspectionRequestTransitioned with from/to', function () {
    Event::fake();
    $actor = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $actor->id,
        'assignee_id' => $actor->id,
        'status' => RequestStatus::Open->value,
    ]);

    app(InspectionRequestService::class)->transition($req, RequestStatus::InProgress, $actor);

    Event::assertDispatched(InspectionRequestTransitioned::class, function (InspectionRequestTransitioned $e) {
        return $e->from === RequestStatus::Open && $e->to === RequestStatus::InProgress;
    });
});

test('assign dispatches InspectionRequestAssigned with previous', function () {
    Event::fake();
    $actor = InspectionRequestHelpers::admin();
    $previous = InspectionRequestHelpers::engineer();
    $next = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $previous->id,
    ]);

    app(InspectionRequestService::class)->assign($req, $next, $actor);

    Event::assertDispatched(InspectionRequestAssigned::class, function (InspectionRequestAssigned $e) use ($previous) {
        return $e->previousAssignee?->id === $previous->id;
    });
});

test('reply create dispatches RequestReplyCreated', function () {
    Event::fake();
    $author = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $author->id,
        'assignee_id' => $author->id,
    ]);

    app(RequestReplyService::class)->create($req, ['body' => 'hello'], $author);

    Event::assertDispatched(RequestReplyCreated::class);
});

test('events do not fire if outer transaction rolls back', function () {
    Event::fake();
    $author = InspectionRequestHelpers::engineer();
    $villa = InspectionRequestHelpers::makeVilla();

    try {
        DB::transaction(function () use ($author, $villa) {
            app(InspectionRequestService::class)->create([
                'assignee_id' => InspectionRequestHelpers::engineer()->id,
                'subject_type' => 'villa',
                'subject_id' => $villa->id,
                'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
                'title' => 'T',
                'description' => 'D',
                'severity' => 'medium',
            ], $author);

            throw new RuntimeException('force rollback');
        });
    } catch (RuntimeException) {
        // expected
    }

    Event::assertNotDispatched(InspectionRequestCreated::class);
});

test('broadcastOn returns expected channels', function () {
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $event = new InspectionRequestCreated($req);
    $channels = collect($event->broadcastOn())->pluck('name')->all();

    expect($channels)->toContain('private-inspection-requests.'.$req->id)
        ->and($channels)->toContain('private-users.'.$req->assignee_id.'.notifications')
        ->and($channels)->toContain('private-users.'.$req->requester_id.'.notifications');
});
