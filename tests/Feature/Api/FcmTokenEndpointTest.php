<?php

use App\Models\FcmToken;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('authenticated user can register a token', function () {
    $user = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/fcm-tokens', [
        'token' => 'sample-token-abc',
        'device_id' => 'pixel-7',
        'platform' => 'android',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('fcm_tokens', [
        'user_id' => $user->id,
        'token' => 'sample-token-abc',
        'platform' => 'android',
    ]);
});

test('re-registering same token upserts to current user', function () {
    $user1 = InspectionRequestHelpers::engineer();
    $user2 = InspectionRequestHelpers::engineer();

    Sanctum::actingAs($user1);
    $this->postJson('/api/v1/fcm-tokens', ['token' => 't1', 'platform' => 'android'])->assertCreated();

    Sanctum::actingAs($user2);
    $this->postJson('/api/v1/fcm-tokens', ['token' => 't1', 'platform' => 'android'])->assertCreated();

    expect(FcmToken::where('token', 't1')->count())->toBe(1)
        ->and(FcmToken::where('token', 't1')->first()->user_id)->toBe($user2->id);
});

test('unauthenticated cannot register', function () {
    $this->postJson('/api/v1/fcm-tokens', ['token' => 'x', 'platform' => 'android'])
        ->assertUnauthorized();
});

test('validation fails on unknown platform', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $this->postJson('/api/v1/fcm-tokens', ['token' => 'x', 'platform' => 'blackberry'])
        ->assertUnprocessable()->assertJsonValidationErrors(['platform']);
});

test('user can delete own token', function () {
    $user = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($user);
    FcmToken::create(['user_id' => $user->id, 'token' => 'mine-xyz', 'platform' => 'web']);

    $this->deleteJson('/api/v1/fcm-tokens/mine-xyz')->assertNoContent();
    $this->assertDatabaseMissing('fcm_tokens', ['token' => 'mine-xyz']);
});

test('user cannot delete another user token', function () {
    $user1 = InspectionRequestHelpers::engineer();
    $user2 = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($user1);
    FcmToken::create(['user_id' => $user2->id, 'token' => 'theirs', 'platform' => 'web']);

    $this->deleteJson('/api/v1/fcm-tokens/theirs')->assertNotFound();
});

test('token is masked in response', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $response = $this->postJson('/api/v1/fcm-tokens', [
        'token' => 'long-secret-token-value',
        'platform' => 'android',
    ]);

    expect($response->json('data.token'))->toBe('long-s...');
});
