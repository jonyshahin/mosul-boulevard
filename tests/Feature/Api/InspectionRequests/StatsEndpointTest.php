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

test('stats returns counts per status, severity, request type, and overdue', function () {
    Sanctum::actingAs(InspectionRequestHelpers::admin());
    $villa = InspectionRequestHelpers::makeVilla();
    $eng = InspectionRequestHelpers::engineer();
    $type = InspectionRequestHelpers::activeRequestType();

    InspectionRequest::factory()->count(2)->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'request_type_id' => $type->id,
        'status' => RequestStatus::Open->value,
        'severity' => RequestSeverity::High->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $eng->id, 'assignee_id' => $eng->id,
        'request_type_id' => $type->id,
        'status' => RequestStatus::Open->value,
        'severity' => RequestSeverity::Medium->value,
        'due_date' => now()->subDay(),
    ]);

    $response = $this->getJson('/api/v1/inspection-requests/stats');

    $response->assertOk();

    expect($response->json('data.by_status.open'))->toBe(3)
        ->and($response->json('data.by_status.resolved'))->toBe(0)
        ->and($response->json('data.by_severity.high'))->toBe(2)
        ->and($response->json('data.by_severity.medium'))->toBe(1)
        ->and($response->json('data.overdue'))->toBe(1)
        ->and($response->json("data.by_request_type.{$type->id}"))->toBe(3);
});

test('stats rejects customer', function () {
    Sanctum::actingAs(InspectionRequestHelpers::customer());

    $this->getJson('/api/v1/inspection-requests/stats')->assertForbidden();
});
