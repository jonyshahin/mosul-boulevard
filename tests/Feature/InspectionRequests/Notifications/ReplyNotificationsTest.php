<?php

use App\Models\InspectionRequest;
use App\Notifications\InspectionRequestRepliedTo;
use App\Services\RequestReplyService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
    Notification::fake();
});

test('author is not notified of own reply; others are', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
    ]);

    app(RequestReplyService::class)->create($req, ['body' => 'from requester'], $requester);

    Notification::assertSentTo($assignee, InspectionRequestRepliedTo::class);
    Notification::assertNotSentTo($requester, InspectionRequestRepliedTo::class);
});

test('assignee reply notifies requester', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
    ]);

    app(RequestReplyService::class)->create($req, ['body' => 'from assignee'], $assignee);

    Notification::assertSentTo($requester, InspectionRequestRepliedTo::class);
    Notification::assertNotSentTo($assignee, InspectionRequestRepliedTo::class);
});

test('third-party admin reply notifies both requester and assignee', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $admin = InspectionRequestHelpers::admin();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
    ]);

    app(RequestReplyService::class)->create($req, ['body' => 'admin chime'], $admin);

    Notification::assertSentTo($requester, InspectionRequestRepliedTo::class);
    Notification::assertSentTo($assignee, InspectionRequestRepliedTo::class);
    Notification::assertNotSentTo($admin, InspectionRequestRepliedTo::class);
});
