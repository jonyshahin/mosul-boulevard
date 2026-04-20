<?php

use App\Events\InspectionRequestCreated;
use App\Events\InspectionRequestOverdue;
use App\Events\RequestReplyCreated;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('inspection-requests.{id} channel authorizes viewers', function () {
    $admin = InspectionRequestHelpers::admin();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $admin->id, 'assignee_id' => $admin->id,
    ]);

    expect(Gate::forUser($admin)->allows('view', $req))->toBeTrue();
});

test('inspection-requests.{id} channel denies customer', function () {
    $customer = InspectionRequestHelpers::customer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    expect(Gate::forUser($customer)->allows('view', $req))->toBeFalse();
});

test('reply created broadcasts on parent request channel + user channels', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id, 'assignee_id' => $assignee->id,
    ]);
    $reply = RequestReply::factory()->for($req, 'request')->create([
        'author_id' => $assignee->id,
    ]);

    $channels = collect((new RequestReplyCreated($reply))->broadcastOn())
        ->filter(fn ($c) => $c instanceof PrivateChannel)
        ->pluck('name');

    expect($channels)->toContain('private-inspection-requests.'.$req->id)
        ->and($channels)->toContain('private-users.'.$assignee->id.'.notifications')
        ->and($channels)->toContain('private-users.'.$requester->id.'.notifications');
});

test('overdue broadcasts include assignee channel', function () {
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
    ]);

    $channels = collect((new InspectionRequestOverdue($req))->broadcastOn())
        ->pluck('name');

    expect($channels)->toContain('private-users.'.$assignee->id.'.notifications');
});

test('broadcastWith payload is lean (no full model)', function () {
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);
    $req->loadMissing('subject');

    $payload = (new InspectionRequestCreated($req))->broadcastWith();

    expect($payload)->toHaveKeys(['id', 'title', 'severity', 'status', 'subject'])
        ->and($payload['id'])->toBe($req->id)
        ->and($payload)->not->toHaveKey('description');
});
