<?php

use App\Enums\RequestStatus;
use App\Events\InspectionRequestOverdue;
use App\Models\InspectionRequest;
use App\Notifications\InspectionRequestOverdueReminder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('finds overdue requests and dispatches event', function () {
    Event::fake();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Open->value,
    ]);

    Artisan::call('inspection-requests:check-overdue');

    Event::assertDispatched(InspectionRequestOverdue::class);
});

test('skips verified and closed', function () {
    Event::fake();
    $eng = InspectionRequestHelpers::engineer();
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Verified->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Closed->value,
    ]);

    Artisan::call('inspection-requests:check-overdue');

    Event::assertNotDispatched(InspectionRequestOverdue::class);
});

test('skips already-notified within 24h', function () {
    Event::fake();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Open->value,
        'overdue_notified_at' => now()->subHours(2),
    ]);

    Artisan::call('inspection-requests:check-overdue');

    Event::assertNotDispatched(InspectionRequestOverdue::class);
});

test('assignee receives OverdueReminder via listener', function () {
    Notification::fake();
    $assignee = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Open->value,
    ]);

    Artisan::call('inspection-requests:check-overdue');

    Notification::assertSentTo($assignee, InspectionRequestOverdueReminder::class);
});

test('command stamps overdue_notified_at', function () {
    $eng = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(2),
        'status' => RequestStatus::Open->value,
    ]);

    Artisan::call('inspection-requests:check-overdue');

    expect($req->fresh()->overdue_notified_at)->not->toBeNull();
});
