<?php

use App\Enums\RequestSeverity;
use App\Models\InspectionRequest;
use App\Models\NotificationRecipientRule;
use App\Notifications\AdditionalRecipientNotification;
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

function ir_make_request(int $severityUserId, string $severity = 'medium'): InspectionRequest
{
    return app(InspectionRequestService::class)->create([
        'assignee_id' => $severityUserId,
        'subject_type' => 'villa',
        'subject_id' => InspectionRequestHelpers::makeVilla()->id,
        'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
        'title' => 'T',
        'description' => 'D',
        'severity' => $severity,
    ], InspectionRequestHelpers::engineer());
}

test('rule matching by type only notifies additional recipient', function () {
    $recipient = InspectionRequestHelpers::engineer();
    $type = InspectionRequestHelpers::activeRequestType();

    NotificationRecipientRule::create([
        'request_type_id' => $type->id,
        'severity' => null,
        'recipient_user_id' => $recipient->id,
    ]);

    ir_make_request(InspectionRequestHelpers::engineer()->id, 'low');

    Notification::assertSentTo($recipient, AdditionalRecipientNotification::class);
});

test('rule matching by severity only', function () {
    $recipient = InspectionRequestHelpers::engineer();

    NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => RequestSeverity::Critical->value,
        'recipient_user_id' => $recipient->id,
    ]);

    ir_make_request(InspectionRequestHelpers::engineer()->id, 'critical');

    Notification::assertSentTo($recipient, AdditionalRecipientNotification::class);
});

test('rule matching both type and severity', function () {
    $recipient = InspectionRequestHelpers::engineer();
    $type = InspectionRequestHelpers::activeRequestType();

    NotificationRecipientRule::create([
        'request_type_id' => $type->id,
        'severity' => RequestSeverity::High->value,
        'recipient_user_id' => $recipient->id,
    ]);

    ir_make_request(InspectionRequestHelpers::engineer()->id, 'high');

    Notification::assertSentTo($recipient, AdditionalRecipientNotification::class);
});

test('wildcard rule (both null) matches every request', function () {
    $recipient = InspectionRequestHelpers::engineer();

    NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => $recipient->id,
    ]);

    ir_make_request(InspectionRequestHelpers::engineer()->id, 'low');

    Notification::assertSentTo($recipient, AdditionalRecipientNotification::class);
});

test('inactive rule is skipped', function () {
    $recipient = InspectionRequestHelpers::engineer();
    $type = InspectionRequestHelpers::activeRequestType();

    NotificationRecipientRule::create([
        'request_type_id' => $type->id,
        'severity' => null,
        'recipient_user_id' => $recipient->id,
        'is_active' => false,
    ]);

    ir_make_request(InspectionRequestHelpers::engineer()->id);

    Notification::assertNotSentTo($recipient, AdditionalRecipientNotification::class);
});

test('assignee covered by rule is NOT double-notified', function () {
    $assignee = InspectionRequestHelpers::engineer();

    NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => $assignee->id,
    ]);

    ir_make_request($assignee->id);

    Notification::assertSentTo($assignee, InspectionRequestAssigned::class);
    Notification::assertNotSentTo($assignee, AdditionalRecipientNotification::class);
});

test('requester covered by rule is not notified as additional', function () {
    $requester = InspectionRequestHelpers::engineer();

    NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => $requester->id,
    ]);

    app(InspectionRequestService::class)->create([
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'subject_type' => 'villa',
        'subject_id' => InspectionRequestHelpers::makeVilla()->id,
        'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
        'title' => 'T',
        'description' => 'D',
        'severity' => 'medium',
    ], $requester);

    Notification::assertNotSentTo($requester, AdditionalRecipientNotification::class);
});

test('severity mismatch: rule for high does not match low', function () {
    $recipient = InspectionRequestHelpers::engineer();

    NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => RequestSeverity::High->value,
        'recipient_user_id' => $recipient->id,
    ]);

    ir_make_request(InspectionRequestHelpers::engineer()->id, 'low');

    Notification::assertNotSentTo($recipient, AdditionalRecipientNotification::class);
});
