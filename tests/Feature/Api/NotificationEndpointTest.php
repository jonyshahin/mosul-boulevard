<?php

use App\Models\InspectionRequest;
use App\Models\User;
use App\Notifications\InspectionRequestAssigned;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

function ir_notify(int $count = 3): User
{
    $user = InspectionRequestHelpers::engineer();
    for ($i = 0; $i < $count; $i++) {
        $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
            'requester_id' => $user->id, 'assignee_id' => $user->id,
        ]);
        $user->notifyNow(new InspectionRequestAssigned($req));
    }

    return $user;
}

test('lists own notifications paginated', function () {
    $user = ir_notify(5);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/notifications');

    $response->assertOk()->assertJsonStructure(['data', 'meta']);
    expect($response->json('meta.total'))->toBe(5);
});

test('unread_only filter narrows results', function () {
    $user = ir_notify(3);
    Sanctum::actingAs($user);
    $user->notifications()->first()->markAsRead();

    $response = $this->getJson('/api/v1/notifications?unread_only=1');

    expect($response->json('meta.total'))->toBe(2);
});

test('mark-read sets read_at', function () {
    $user = ir_notify(1);
    Sanctum::actingAs($user);
    $notif = $user->notifications()->first();

    $this->postJson("/api/v1/notifications/{$notif->id}/read")->assertOk();

    expect($user->notifications()->first()->read_at)->not->toBeNull();
});

test('mark-all-read clears unread', function () {
    $user = ir_notify(3);
    Sanctum::actingAs($user);

    $this->postJson('/api/v1/notifications/mark-all-read')->assertNoContent();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('unread-count returns integer count', function () {
    $user = ir_notify(2);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/notifications/unread-count');

    $response->assertOk();
    expect($response->json('data.count'))->toBe(2);
});

test('user cannot see another user notifications', function () {
    $owner = ir_notify(1);
    $other = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($other);

    expect($this->getJson('/api/v1/notifications')->json('meta.total'))->toBe(0);
});

test('unauthenticated 401', function () {
    $this->getJson('/api/v1/notifications')->assertUnauthorized();
});
