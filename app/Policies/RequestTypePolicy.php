<?php

namespace App\Policies;

use App\Models\RequestType;
use App\Models\User;

class RequestTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEngineer() || $user->isViewer();
    }

    public function view(User $user, RequestType $type): bool
    {
        return $user->isAdmin() || $user->isEngineer() || $user->isViewer();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, RequestType $type): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, RequestType $type): bool
    {
        return $user->isAdmin();
    }
}
