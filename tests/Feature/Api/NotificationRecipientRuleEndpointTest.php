<?php

use App\Models\NotificationRecipientRule;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('admin can list rules', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $response = $this->getJson('/api/v1/notification-recipient-rules');

    $response->assertOk()->assertJsonStructure(['data', 'meta']);
});

test('engineer cannot list', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());

    $this->getJson('/api/v1/notification-recipient-rules')->assertForbidden();
});

test('admin creates a rule', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $recipient = InspectionRequestHelpers::engineer();
    $type = InspectionRequestHelpers::activeRequestType();

    $response = $this->postJson('/api/v1/notification-recipient-rules', [
        'request_type_id' => $type->id,
        'severity' => 'high',
        'recipient_user_id' => $recipient->id,
        'is_active' => true,
        'sort_order' => 10,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('notification_recipient_rules', [
        'request_type_id' => $type->id,
        'severity' => 'high',
        'recipient_user_id' => $recipient->id,
    ]);
});

test('rejects customer as recipient', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $this->postJson('/api/v1/notification-recipient-rules', [
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => InspectionRequestHelpers::customer()->id,
    ])->assertUnprocessable()->assertJsonValidationErrors(['recipient_user_id']);
});

test('admin updates a rule', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $rule = NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => InspectionRequestHelpers::engineer()->id,
        'is_active' => true,
    ]);

    $this->patchJson("/api/v1/notification-recipient-rules/{$rule->id}", [
        'is_active' => false,
    ])->assertOk();

    expect($rule->fresh()->is_active)->toBeFalse();
});

test('admin deletes a rule', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $rule = NotificationRecipientRule::create([
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->deleteJson("/api/v1/notification-recipient-rules/{$rule->id}")->assertNoContent();
    $this->assertDatabaseMissing('notification_recipient_rules', ['id' => $rule->id]);
});

test('allows both wildcards (type and severity null)', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $this->postJson('/api/v1/notification-recipient-rules', [
        'request_type_id' => null,
        'severity' => null,
        'recipient_user_id' => InspectionRequestHelpers::engineer()->id,
    ])->assertCreated();
});
