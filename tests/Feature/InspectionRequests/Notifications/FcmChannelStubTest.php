<?php

use App\Models\FcmNotificationLog;
use App\Models\FcmToken;
use App\Models\InspectionRequest;
use App\Notifications\InspectionRequestAssigned;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('notifying a user with one fcm token creates one stub log row', function () {
    $user = InspectionRequestHelpers::engineer();
    FcmToken::create([
        'user_id' => $user->id,
        'token' => 'fake-token-abc',
        'platform' => 'android',
    ]);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $user->id, 'assignee_id' => $user->id,
    ]);

    $user->notify(new InspectionRequestAssigned($req));

    expect(FcmNotificationLog::count())->toBe(1);
    $log = FcmNotificationLog::first();
    expect($log->status)->toBe('stub')
        ->and($log->user_id)->toBe($user->id)
        ->and($log->token)->toBe('fake-token-abc');
});

test('user with multiple devices gets one log per device', function () {
    $user = InspectionRequestHelpers::engineer();
    FcmToken::create(['user_id' => $user->id, 'token' => 'phone-xyz', 'platform' => 'android']);
    FcmToken::create(['user_id' => $user->id, 'token' => 'web-xyz', 'platform' => 'web']);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $user->id, 'assignee_id' => $user->id,
    ]);

    $user->notify(new InspectionRequestAssigned($req));

    expect(FcmNotificationLog::where('user_id', $user->id)->count())->toBe(2);
});

test('user with no tokens produces no log rows', function () {
    $user = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $user->id, 'assignee_id' => $user->id,
    ]);

    $user->notify(new InspectionRequestAssigned($req));

    expect(FcmNotificationLog::count())->toBe(0);
});
