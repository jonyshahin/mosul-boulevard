<?php

use App\Enums\RequestStatus;

dataset('valid_transitions', [
    'open → in_progress' => [RequestStatus::Open, RequestStatus::InProgress],
    'open → resolved' => [RequestStatus::Open, RequestStatus::Resolved],
    'open → closed' => [RequestStatus::Open, RequestStatus::Closed],
    'in_progress → resolved' => [RequestStatus::InProgress, RequestStatus::Resolved],
    'in_progress → open' => [RequestStatus::InProgress, RequestStatus::Open],
    'resolved → verified' => [RequestStatus::Resolved, RequestStatus::Verified],
    'resolved → reopened' => [RequestStatus::Resolved, RequestStatus::Reopened],
    'verified → closed' => [RequestStatus::Verified, RequestStatus::Closed],
    'closed → reopened' => [RequestStatus::Closed, RequestStatus::Reopened],
    'reopened → in_progress' => [RequestStatus::Reopened, RequestStatus::InProgress],
    'reopened → resolved' => [RequestStatus::Reopened, RequestStatus::Resolved],
]);

dataset('invalid_transitions', [
    'open → verified' => [RequestStatus::Open, RequestStatus::Verified],
    'open → reopened' => [RequestStatus::Open, RequestStatus::Reopened],
    'in_progress → verified' => [RequestStatus::InProgress, RequestStatus::Verified],
    'in_progress → closed' => [RequestStatus::InProgress, RequestStatus::Closed],
    'in_progress → reopened' => [RequestStatus::InProgress, RequestStatus::Reopened],
    'resolved → open' => [RequestStatus::Resolved, RequestStatus::Open],
    'resolved → in_progress' => [RequestStatus::Resolved, RequestStatus::InProgress],
    'resolved → closed' => [RequestStatus::Resolved, RequestStatus::Closed],
    'verified → open' => [RequestStatus::Verified, RequestStatus::Open],
    'verified → reopened' => [RequestStatus::Verified, RequestStatus::Reopened],
    'closed → open' => [RequestStatus::Closed, RequestStatus::Open],
    'closed → in_progress' => [RequestStatus::Closed, RequestStatus::InProgress],
    'reopened → open' => [RequestStatus::Reopened, RequestStatus::Open],
    'reopened → verified' => [RequestStatus::Reopened, RequestStatus::Verified],
    'reopened → closed' => [RequestStatus::Reopened, RequestStatus::Closed],
]);

test('valid transition returns true', function (RequestStatus $from, RequestStatus $to) {
    expect($from->canTransitionTo($to))->toBeTrue();
})->with('valid_transitions');

test('invalid transition returns false', function (RequestStatus $from, RequestStatus $to) {
    expect($from->canTransitionTo($to))->toBeFalse();
})->with('invalid_transitions');

test('validTransitions returns map for every state', function () {
    $map = RequestStatus::validTransitions();

    foreach (RequestStatus::cases() as $status) {
        expect($map)->toHaveKey($status->value);
    }
});

test('only closed is terminal', function () {
    foreach (RequestStatus::cases() as $status) {
        expect($status->isTerminal())->toBe($status === RequestStatus::Closed);
    }
});
