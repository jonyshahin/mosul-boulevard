<?php

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    Storage::fake('r2');
    InspectionRequestHelpers::seedRequiredLookups();
});

test('returns paginated envelope with meta', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    InspectionRequest::factory()->count(3)->forVilla($villa)->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    $response = $this->getJson('/api/v1/inspection-requests');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);

    expect($response->json('meta.total'))->toBe(3)
        ->and($response->json('meta.per_page'))->toBe(20);
});

test('respects per_page clamped at 100', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());

    $response = $this->getJson('/api/v1/inspection-requests?per_page=500');

    $response->assertOk();
    expect($response->json('meta.per_page'))->toBe(100);
});

test('customer receives 403', function () {
    Sanctum::actingAs(InspectionRequestHelpers::customer());

    $this->getJson('/api/v1/inspection-requests')->assertForbidden();
});

test('unauthenticated receives 401', function () {
    $this->getJson('/api/v1/inspection-requests')->assertUnauthorized();
});

test('status filter narrows the set', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'status' => RequestStatus::Open->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'status' => RequestStatus::Resolved->value]);

    $response = $this->getJson('/api/v1/inspection-requests?status[]=open');

    $response->assertOk();
    expect($response->json('meta.total'))->toBe(1);
});

test('severity filter narrows the set', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::High->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id, 'severity' => RequestSeverity::Low->value]);

    $response = $this->getJson('/api/v1/inspection-requests?severity[]=high');

    expect($response->json('meta.total'))->toBe(1);
});

test('assignee and requester filters work', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    $alice = InspectionRequestHelpers::engineer();
    $bob = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $alice->id, 'assignee_id' => $bob->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['requester_id' => $bob->id, 'assignee_id' => $alice->id]);

    expect($this->getJson("/api/v1/inspection-requests?assignee_id={$alice->id}")->json('meta.total'))->toBe(1);
    expect($this->getJson("/api/v1/inspection-requests?requester_id={$alice->id}")->json('meta.total'))->toBe(1);
});

test('subject filter scopes to villa or tower_unit', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $v1 = InspectionRequestHelpers::makeVilla();
    $t1 = InspectionRequestHelpers::makeTowerUnit();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($v1)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);
    InspectionRequest::factory()->forTowerUnit($t1)->create(['requester_id' => $eng->id, 'assignee_id' => $eng->id]);

    expect($this->getJson("/api/v1/inspection-requests?subject_type=villa&subject_id={$v1->id}")->json('meta.total'))->toBe(1);
    expect($this->getJson("/api/v1/inspection-requests?subject_type=tower_unit&subject_id={$t1->id}")->json('meta.total'))->toBe(1);
});

test('overdue flag surfaces the overdue scope', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->subDays(1),
        'status' => RequestStatus::Open->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'due_date' => now()->addDays(5),
        'status' => RequestStatus::Open->value,
    ]);

    expect($this->getJson('/api/v1/inspection-requests?overdue=1')->json('meta.total'))->toBe(1);
});

test('search matches title or description', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();

    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'title' => 'Formwork misalignment on west side',
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'title' => 'Unrelated',
    ]);

    expect($this->getJson('/api/v1/inspection-requests?search=Formwork')->json('meta.total'))->toBe(1);
});
