<?php

use App\Models\InspectionRequest;
use App\Models\RequestMedia;
use App\Models\RequestReply;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('reply factory produces a valid reply', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    $reply = RequestReply::factory()->for($request, 'request')->create();

    expect($reply->exists)->toBeTrue()
        ->and($reply->inspection_request_id)->toBe($request->id)
        ->and($reply->body)->not->toBeEmpty();
});

test('reply belongs to request', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();
    $reply = RequestReply::factory()->for($request, 'request')->create();

    expect($reply->request)->toBeInstanceOf(InspectionRequest::class)
        ->and($reply->request->is($request))->toBeTrue();
});

test('reply media morphMany works', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();
    $reply = RequestReply::factory()->for($request, 'request')->create();

    RequestMedia::factory()->count(2)->create([
        'mediable_type' => $reply->getMorphClass(),
        'mediable_id' => $reply->id,
    ]);

    expect($reply->media()->count())->toBe(2);
});
