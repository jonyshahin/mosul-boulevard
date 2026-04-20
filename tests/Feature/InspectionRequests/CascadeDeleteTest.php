<?php

use App\Models\InspectionRequest;
use App\Models\RequestMedia;
use App\Models\RequestReply;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('soft-deleting a request keeps it in the database with deleted_at', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    $request->delete();

    expect(InspectionRequest::find($request->id))->toBeNull()
        ->and(InspectionRequest::withTrashed()->find($request->id))->not->toBeNull()
        ->and(InspectionRequest::withTrashed()->find($request->id)->deleted_at)->not->toBeNull();
});

test('force-deleting a request cascades to replies', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    RequestReply::factory()->count(3)->for($request, 'request')->create();

    expect(RequestReply::where('inspection_request_id', $request->id)->count())->toBe(3);

    $request->forceDelete();

    expect(RequestReply::where('inspection_request_id', $request->id)->count())->toBe(0);
});

test('deleting a reply cascades to its media', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();
    $reply = RequestReply::factory()->for($request, 'request')->create();

    RequestMedia::factory()->count(2)->create([
        'mediable_type' => $reply->getMorphClass(),
        'mediable_id' => $reply->id,
    ]);

    expect(RequestMedia::where('mediable_type', $reply->getMorphClass())
        ->where('mediable_id', $reply->id)->count())->toBe(2);

    $reply->delete();

    expect(RequestMedia::where('mediable_type', $reply->getMorphClass())
        ->where('mediable_id', $reply->id)->count())->toBe(0);
});
