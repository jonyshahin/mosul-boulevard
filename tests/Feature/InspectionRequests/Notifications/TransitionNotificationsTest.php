<?php

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Notifications\InspectionRequestTransitioned as TransitionedNotification;
use App\Services\InspectionRequestService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
    Notification::fake();
});

test('requester notified when assignee resolves', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::InProgress->value,
    ]);

    app(InspectionRequestService::class)->transition($req, RequestStatus::Resolved, $assignee);

    Notification::assertSentTo($requester, TransitionedNotification::class);
    Notification::assertNotSentTo($assignee, TransitionedNotification::class);
});

test('assignee notified when requester reopens', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Closed->value,
    ]);

    app(InspectionRequestService::class)->transition($req, RequestStatus::Reopened, $requester);

    Notification::assertSentTo($assignee, TransitionedNotification::class);
    Notification::assertNotSentTo($requester, TransitionedNotification::class);
});

test('requester is same as assignee → no self-notification', function () {
    $same = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $same->id,
        'assignee_id' => $same->id,
        'status' => RequestStatus::InProgress->value,
    ]);

    app(InspectionRequestService::class)->transition($req, RequestStatus::Resolved, $same);

    Notification::assertNotSentTo($same, TransitionedNotification::class);
});
