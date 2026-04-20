<?php

use App\Models\InspectionRequest;
use App\Notifications\InspectionRequestAssigned;
use App\Services\InspectionRequestService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
    Notification::fake();
});

test('assignee gets InspectionRequestAssigned on create', function () {
    $author = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $villa = InspectionRequestHelpers::makeVilla();

    app(InspectionRequestService::class)->create([
        'assignee_id' => $assignee->id,
        'subject_type' => 'villa',
        'subject_id' => $villa->id,
        'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
        'title' => 'T',
        'description' => 'D',
        'severity' => 'medium',
    ], $author);

    Notification::assertSentTo($assignee, InspectionRequestAssigned::class);
});

test('assignee notification fans to database mail fcm broadcast', function () {
    $author = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $author->id,
        'assignee_id' => $assignee->id,
    ]);

    $assignee->notify(new InspectionRequestAssigned($req));

    Notification::assertSentTo($assignee, InspectionRequestAssigned::class, function ($notification, array $channels) {
        return in_array('database', $channels)
            && in_array('mail', $channels)
            && in_array('fcm', $channels)
            && in_array('broadcast', $channels);
    });
});

test('customer assignee opts out of all channels', function () {
    $customer = InspectionRequestHelpers::customer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $customer->id,
    ]);

    $notification = new InspectionRequestAssigned($req);

    expect($customer->notificationChannels())->toBe([])
        ->and($notification->via($customer))->toBe([]);
});
