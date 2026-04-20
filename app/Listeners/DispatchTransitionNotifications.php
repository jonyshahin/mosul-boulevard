<?php

namespace App\Listeners;

use App\Enums\RequestStatus;
use App\Events\InspectionRequestTransitioned;
use App\Models\User;
use App\Notifications\InspectionRequestTransitioned as TransitionedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\AsEventListener;

#[AsEventListener(event: InspectionRequestTransitioned::class)]
class DispatchTransitionNotifications implements ShouldQueue
{

    public string $queue = 'notifications';

    public function handle(InspectionRequestTransitioned $event): void
    {
        $request = $event->request;
        $actor = $event->actor;
        $to = $event->to;

        $notifyIds = [];

        if (
            $actor->id === $request->assignee_id
            && in_array($to, [RequestStatus::Resolved, RequestStatus::Verified, RequestStatus::Closed, RequestStatus::Reopened], true)
            && $actor->id !== $request->requester_id
        ) {
            $notifyIds[] = $request->requester_id;
        }

        if (
            $actor->id === $request->requester_id
            && $to === RequestStatus::Reopened
            && $actor->id !== $request->assignee_id
        ) {
            $notifyIds[] = $request->assignee_id;
        }

        foreach (array_unique(array_filter($notifyIds)) as $userId) {
            if ($user = User::find($userId)) {
                $user->notify(new TransitionedNotification(
                    request: $request,
                    from: $event->from,
                    to: $event->to,
                    actor: $actor,
                ));
            }
        }
    }
}
