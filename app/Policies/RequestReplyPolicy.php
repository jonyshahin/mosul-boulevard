<?php

namespace App\Policies;

use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\User;

class RequestReplyPolicy
{
    public function create(User $user, InspectionRequest $request): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isEngineer()) {
            return false;
        }

        return $user->id === $request->requester_id
            || $user->id === $request->assignee_id;
    }

    public function delete(User $user, RequestReply $reply): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id !== $reply->author_id) {
            return false;
        }

        $window = (int) config('inspection_requests.reply_edit_window_minutes');

        return $reply->created_at?->gt(now()->subMinutes($window)) ?? false;
    }
}
