<?php

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('assignee can create reply without media', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
    ]);

    $response = $this->postJson("/api/v1/inspection-requests/{$req->id}/replies", [
        'body' => 'Checked today, in progress.',
    ]);

    $response->assertCreated()->assertJsonPath('data.body', 'Checked today, in progress.');
});

test('reply with media uploads file', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
    ]);

    $response = $this->postJson("/api/v1/inspection-requests/{$req->id}/replies", [
        'body' => 'Photos attached',
        'media' => [UploadedFile::fake()->image('status.jpg')],
    ]);

    $response->assertCreated();

    $reply = RequestReply::with('media')->latest()->first();
    expect($reply->media)->toHaveCount(1);
    Storage::disk('r2')->assertExists($reply->media->first()->path);
});

test('reply with triggers_status transitions the request atomically', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Open->value,
    ]);

    $response = $this->postJson("/api/v1/inspection-requests/{$req->id}/replies", [
        'body' => 'Moving to in_progress',
        'triggers_status' => RequestStatus::InProgress->value,
    ]);

    $response->assertCreated();
    expect($req->fresh()->status->value)->toBe('in_progress');
});

test('engineer not assignee nor requester cannot reply', function () {
    Sanctum::actingAs(InspectionRequestHelpers::engineer());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/replies", ['body' => 'Nope'])
        ->assertForbidden();
});

test('invalid triggers_status returns 422', function () {
    $assignee = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($assignee);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
        'status' => RequestStatus::Open->value,
    ]);

    $this->postJson("/api/v1/inspection-requests/{$req->id}/replies", [
        'body' => 'Try to verify from open',
        'triggers_status' => RequestStatus::Verified->value,
    ])->assertUnprocessable();
});

test('reply delete within window', function () {
    $author = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($author);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $author->id,
        'assignee_id' => $author->id,
    ]);
    $reply = RequestReply::factory()->for($req, 'request')->create([
        'author_id' => $author->id,
    ]);

    $this->deleteJson("/api/v1/replies/{$reply->id}")->assertNoContent();
});

test('reply delete outside window forbidden', function () {
    $author = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($author);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $author->id,
        'assignee_id' => $author->id,
    ]);
    $reply = RequestReply::factory()->for($req, 'request')->create([
        'author_id' => $author->id,
        'created_at' => now()->subHour(),
    ]);

    $this->deleteJson("/api/v1/replies/{$reply->id}")->assertForbidden();
});

test('index lists replies for the request', function () {
    $actor = InspectionRequestHelpers::admin();
    Sanctum::actingAs($actor);

    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);
    RequestReply::factory()->count(3)->for($req, 'request')->create([
        'author_id' => $actor->id,
    ]);

    $response = $this->getJson("/api/v1/inspection-requests/{$req->id}/replies");

    $response->assertOk();
    expect($response->json('meta.total'))->toBe(3);
});
