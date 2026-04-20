<?php

namespace App\Policies;

use App\Models\InspectionRequest;
use App\Models\RequestMedia;
use App\Models\RequestReply;
use App\Models\User;

class RequestMediaPolicy
{
    public function view(User $user, RequestMedia $media): bool
    {
        $parent = $media->mediable;
        $request = $parent instanceof RequestReply
            ? $parent->request
            : ($parent instanceof InspectionRequest ? $parent : null);

        if (! $request) {
            return false;
        }

        return app(InspectionRequestPolicy::class)->view($user, $request);
    }

    public function delete(User $user, RequestMedia $media): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id !== $media->uploaded_by) {
            return false;
        }

        $window = (int) config('inspection_requests.reply_edit_window_minutes');

        return $media->created_at?->gt(now()->subMinutes($window)) ?? false;
    }
}
