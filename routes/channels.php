<?php

use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('inspection-requests.{id}', function (User $user, int $id): bool {
    $request = InspectionRequest::find($id);

    if (! $request) {
        return false;
    }

    return Gate::forUser($user)->allows('view', $request);
});

Broadcast::channel('users.{id}.notifications', function (User $user, int $id): bool {
    return (int) $user->id === (int) $id;
});
