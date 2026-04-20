<?php

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Policies\InspectionRequestPolicy;
use App\Policies\RequestReplyPolicy;
use App\Policies\RequestTypePolicy;
use Tests\Support\InspectionRequestHelpers;

beforeEach(function () {
    InspectionRequestHelpers::seedRequiredLookups();
    $this->policy = new InspectionRequestPolicy;
});

test('admin can view, create, update, delete, assign', function () {
    $admin = InspectionRequestHelpers::admin();
    $villa = InspectionRequestHelpers::makeVilla();
    $req = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
    ]);

    expect($this->policy->viewAny($admin))->toBeTrue()
        ->and($this->policy->view($admin, $req))->toBeTrue()
        ->and($this->policy->create($admin))->toBeTrue()
        ->and($this->policy->update($admin, $req))->toBeTrue()
        ->and($this->policy->delete($admin, $req))->toBeTrue()
        ->and($this->policy->assign($admin, $req))->toBeTrue();
});

test('engineer can view and create but not delete', function () {
    $engineer = InspectionRequestHelpers::engineer();

    expect($this->policy->viewAny($engineer))->toBeTrue()
        ->and($this->policy->create($engineer))->toBeTrue()
        ->and($this->policy->delete($engineer, InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
            'requester_id' => $engineer->id,
            'assignee_id' => $engineer->id,
        ])))->toBeFalse();
});

test('viewer can view but not create', function () {
    $viewer = InspectionRequestHelpers::viewer();

    expect($this->policy->viewAny($viewer))->toBeTrue()
        ->and($this->policy->create($viewer))->toBeFalse();
});

test('customer cannot view', function () {
    $customer = InspectionRequestHelpers::customer();

    expect($this->policy->viewAny($customer))->toBeFalse()
        ->and($this->policy->create($customer))->toBeFalse();
});

test('update allowed for requester while status is open or in_progress', function () {
    $requester = InspectionRequestHelpers::engineer();
    $villa = InspectionRequestHelpers::makeVilla();

    $open = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'status' => RequestStatus::Open->value,
    ]);
    $inProgress = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'status' => RequestStatus::InProgress->value,
    ]);
    $resolved = InspectionRequest::factory()->forVilla($villa)->create([
        'requester_id' => $requester->id,
        'assignee_id' => InspectionRequestHelpers::engineer()->id,
        'status' => RequestStatus::Resolved->value,
    ]);

    expect($this->policy->update($requester, $open))->toBeTrue()
        ->and($this->policy->update($requester, $inProgress))->toBeTrue()
        ->and($this->policy->update($requester, $resolved))->toBeFalse();
});

test('update denied for non-requester non-admin', function () {
    $requester = InspectionRequestHelpers::engineer();
    $other = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $other->id,
        'status' => RequestStatus::Open->value,
    ]);

    expect($this->policy->update($other, $req))->toBeFalse();
});

test('transition: open and in_progress require assignee or admin', function () {
    $assignee = InspectionRequestHelpers::engineer();
    $outsider = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => InspectionRequestHelpers::engineer()->id,
        'assignee_id' => $assignee->id,
    ]);

    foreach ([RequestStatus::Open, RequestStatus::InProgress, RequestStatus::Resolved] as $target) {
        expect($this->policy->transition($assignee, $req, $target))->toBeTrue();
        expect($this->policy->transition($outsider, $req, $target))->toBeFalse();
    }
});

test('transition: verified, closed, reopened require requester or admin', function () {
    $requester = InspectionRequestHelpers::engineer();
    $assignee = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $requester->id,
        'assignee_id' => $assignee->id,
    ]);

    foreach ([RequestStatus::Verified, RequestStatus::Closed, RequestStatus::Reopened] as $target) {
        expect($this->policy->transition($requester, $req, $target))->toBeTrue();
        expect($this->policy->transition($assignee, $req, $target))->toBeFalse();
    }
});

test('reply delete allowed within window, denied after', function () {
    $author = InspectionRequestHelpers::engineer();
    $req = InspectionRequest::factory()->forVilla(InspectionRequestHelpers::makeVilla())->create([
        'requester_id' => $author->id,
        'assignee_id' => $author->id,
    ]);

    $fresh = RequestReply::factory()->for($req, 'request')->create([
        'author_id' => $author->id,
        'created_at' => now()->subMinutes(5),
    ]);
    $stale = RequestReply::factory()->for($req, 'request')->create([
        'author_id' => $author->id,
        'created_at' => now()->subMinutes(60),
    ]);

    $policy = new RequestReplyPolicy;

    expect($policy->delete($author, $fresh))->toBeTrue()
        ->and($policy->delete($author, $stale))->toBeFalse();
});

test('request type full CRUD admin only', function () {
    $admin = InspectionRequestHelpers::admin();
    $engineer = InspectionRequestHelpers::engineer();
    $type = InspectionRequestHelpers::activeRequestType();

    $policy = new RequestTypePolicy;

    expect($policy->create($admin))->toBeTrue()
        ->and($policy->create($engineer))->toBeFalse()
        ->and($policy->update($admin, $type))->toBeTrue()
        ->and($policy->update($engineer, $type))->toBeFalse()
        ->and($policy->delete($admin, $type))->toBeTrue()
        ->and($policy->viewAny($engineer))->toBeTrue();
});
