<?php

use App\Enums\MediaType;
use App\Models\InspectionRequest;
use App\Models\RequestMedia;
use App\Models\RequestReply;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('media morphs to InspectionRequest', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    $media = RequestMedia::factory()->create([
        'mediable_type' => $request->getMorphClass(),
        'mediable_id' => $request->id,
    ]);

    expect($media->mediable)->toBeInstanceOf(InspectionRequest::class)
        ->and($media->mediable->is($request))->toBeTrue()
        ->and($media->media_type)->toBe(MediaType::Image);
});

test('media morphs to RequestReply', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();
    $reply = RequestReply::factory()->for($request, 'request')->create();

    $media = RequestMedia::factory()->create([
        'mediable_type' => $reply->getMorphClass(),
        'mediable_id' => $reply->id,
    ]);

    expect($media->mediable)->toBeInstanceOf(RequestReply::class)
        ->and($media->mediable->is($reply))->toBeTrue();
});

test('video state sets mime and media_type', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    $media = RequestMedia::factory()->video()->create([
        'mediable_type' => $request->getMorphClass(),
        'mediable_id' => $request->id,
    ]);

    expect($media->media_type)->toBe(MediaType::Video)
        ->and($media->mime_type)->toStartWith('video/');
});
