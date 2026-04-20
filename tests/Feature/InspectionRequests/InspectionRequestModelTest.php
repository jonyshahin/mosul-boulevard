<?php

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
});

test('factory produces a valid inspection request', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    expect($request->exists)->toBeTrue()
        ->and($request->status)->toBe(RequestStatus::Open)
        ->and($request->severity)->toBe(RequestSeverity::Medium);
});

test('polymorphic subject works for Villa', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $request = InspectionRequest::factory()->forVilla($villa)->create();

    expect($request->subject)->toBeInstanceOf(Villa::class)
        ->and($request->subject->is($villa))->toBeTrue();
});

test('polymorphic subject works for TowerUnit', function () {
    $unit = InspectionRequestHelpers::makeTowerUnit();
    $request = InspectionRequest::factory()->forTowerUnit($unit)->create();

    expect($request->subject)->toBeInstanceOf(TowerUnit::class)
        ->and($request->subject->is($unit))->toBeTrue();
});

test('Villa inspectionRequests relation works', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    InspectionRequest::factory()->count(2)->forVilla($villa)->create();
    InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create();

    expect($villa->inspectionRequests()->count())->toBe(2);
});

test('TowerUnit inspectionRequests relation works', function () {
    $unit = InspectionRequestHelpers::makeTowerUnit();
    InspectionRequest::factory()->count(3)->forTowerUnit($unit)->create();

    expect($unit->inspectionRequests()->count())->toBe(3);
});

test('overdue scope excludes verified and closed', function () {
    $villa = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->forVilla($villa)->overdue()->create();
    InspectionRequest::factory()->forVilla($villa)->overdue()->create([
        'status' => RequestStatus::Verified->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->overdue()->create([
        'status' => RequestStatus::Closed->value,
    ]);
    InspectionRequest::factory()->forVilla($villa)->create([
        'due_date' => now()->addDays(5),
    ]);

    expect(InspectionRequest::overdue()->count())->toBe(1);
});

test('forAssignee scope filters by assignee', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    InspectionRequest::factory()->forVilla($villa)->create(['assignee_id' => $alice->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['assignee_id' => $alice->id]);
    InspectionRequest::factory()->forVilla($villa)->create(['assignee_id' => $bob->id]);

    expect(InspectionRequest::forAssignee($alice->id)->count())->toBe(2);
});

test('forSubject scope filters by subject', function () {
    $villa1 = InspectionRequestHelpers::makeVilla();
    $villa2 = InspectionRequestHelpers::makeVilla();

    InspectionRequest::factory()->count(3)->forVilla($villa1)->create();
    InspectionRequest::factory()->forVilla($villa2)->create();

    expect(InspectionRequest::forSubject($villa1)->count())->toBe(3)
        ->and(InspectionRequest::forSubject($villa2)->count())->toBe(1);
});

test('bySeverity and byStatus scopes filter correctly', function () {
    $villa = InspectionRequestHelpers::makeVilla();
    InspectionRequest::factory()->forVilla($villa)->create(['severity' => RequestSeverity::High->value]);
    InspectionRequest::factory()->forVilla($villa)->create(['severity' => RequestSeverity::Low->value]);
    InspectionRequest::factory()->forVilla($villa)->resolved()->create();

    expect(InspectionRequest::bySeverity(RequestSeverity::High)->count())->toBe(1)
        ->and(InspectionRequest::byStatus(RequestStatus::Resolved)->count())->toBe(1)
        ->and(InspectionRequest::open()->count())->toBe(2);
});
