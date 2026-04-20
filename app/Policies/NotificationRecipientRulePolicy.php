<?php

namespace App\Policies;

use App\Models\NotificationRecipientRule;
use App\Models\User;

class NotificationRecipientRulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, NotificationRecipientRule $rule): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, NotificationRecipientRule $rule): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, NotificationRecipientRule $rule): bool
    {
        return $user->isAdmin();
    }
}
