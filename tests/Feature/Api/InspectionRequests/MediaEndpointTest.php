<?php

use App\Models\InspectionRequest;
use App\Models\RequestMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('download redirects for staff', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);
    Storage::disk('r2')->put('inspection-requests/'.$req->id.'/x.jpg', 'fake-bytes');
    $media = RequestMedia::factory()->create([
        'mediable_type' => $req->getMorphClass(),
        'mediable_id' => $req->id,
        'path' => 'inspection-requests/'.$req->id.'/x.jpg',
    ]);

    $response = $this->getJson("/api/v1/request-media/{$media->id}/download");

    $response->assertRedirect();
});

test('download rejects customer', function () {
    Sanctum::actingAs(InspectionRequestHelpers::customer());
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);
    $media = RequestMedia::factory()->create([
        'mediable_type' => $req->getMorphClass(),
        'mediable_id' => $req->id,
    ]);

    $this->getJson("/api/v1/request-media/{$media->id}/download")->assertForbidden();
});

test('uploader can delete within window', function () {
    $author = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($author);
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $author->id,
        'assignee_id' => $author->id,
    ]);
    Storage::disk('r2')->put('inspection-requests/'.$req->id.'/a.jpg', 'x');
    $media = RequestMedia::factory()->create([
        'mediable_type' => $req->getMorphClass(),
        'mediable_id' => $req->id,
        'path' => 'inspection-requests/'.$req->id.'/a.jpg',
        'uploaded_by' => $author->id,
    ]);

    $this->deleteJson("/api/v1/request-media/{$media->id}")->assertNoContent();

    Storage::disk('r2')->assertMissing('inspection-requests/'.$req->id.'/a.jpg');
    $this->assertDatabaseMissing('request_media', ['id' => $media->id]);
});

test('non-uploader engineer cannot delete media', function () {
    $other = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($other);
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $other->id,
        'assignee_id' => $other->id,
    ]);
    $media = RequestMedia::factory()->create([
        'mediable_type' => $req->getMorphClass(),
        'mediable_id' => $req->id,
        'uploaded_by' => InspectionRequestHelpers::engineer()->id,
    ]);

    $this->deleteJson("/api/v1/request-media/{$media->id}")->assertForbidden();
});

test('end-to-end: create with media, delete the request media via API', function () {
    $author = InspectionRequestHelpers::engineer();
    Sanctum::actingAs($author);

    $response = $this->postJson('/api/v1/inspection-requests', [
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'subject_type' => 'villa',
        'subject_id' => InspectionRequestHelpers::makeVilla()->id,
        'request_type_id' => InspectionRequestHelpers::activeRequestType()->id,
        'title' => 'With media',
        'description' => 'has a photo',
        'severity' => 'medium',
        'media' => [UploadedFile::fake()->image('x.jpg')],
    ]);

    $response->assertCreated();
    $media = RequestMedia::latest()->first();
    Storage::disk('r2')->assertExists($media->path);

    $this->deleteJson("/api/v1/request-media/{$media->id}")->assertNoContent();
    Storage::disk('r2')->assertMissing($media->path);
});
