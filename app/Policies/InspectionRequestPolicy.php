<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\User;

class InspectionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, InspectionRequest $request): bool
    {
        return $this->isStaff($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEngineer();
    }

    public function update(User $user, InspectionRequest $request): bool
    {
        if (! $user->isAdmin() && $user->id !== $request->requester_id) {
            return false;
        }

        return in_array(
            $request->status,
            [RequestStatus::Open, RequestStatus::InProgress],
            true,
        );
    }

    public function delete(User $user, InspectionRequest $request): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, InspectionRequest $request): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, InspectionRequest $request): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user, InspectionRequest $request): bool
    {
        return $user->isAdmin();
    }

    public function transition(User $user, InspectionRequest $request, RequestStatus $target): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return match ($target) {
            RequestStatus::Open,
            RequestStatus::InProgress,
            RequestStatus::Resolved => $user->id === $request->assignee_id,
            RequestStatus::Verified,
            RequestStatus::Closed,
            RequestStatus::Reopened => $user->id === $request->requester_id,
        };
    }

    private function isStaff(User $user): bool
    {
        return $user->isAdmin() || $user->isEngineer() || $user->isViewer();
    }
}
