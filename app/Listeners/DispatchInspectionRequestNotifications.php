<?php

namespace App\Listeners;

use App\Enums\RequestSeverity;
use App\Events\InspectionRequestAssigned;
use App\Events\InspectionRequestCreated;
use App\Models\NotificationRecipientRule;
use App\Models\User;
use App\Notifications\AdditionalRecipientNotification;
use App\Notifications\InspectionRequestAssigned as AssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\AsEventListener;
use Illuminate\Support\Facades\Notification;

#[AsEventListener(event: InspectionRequestCreated::class)]
#[AsEventListener(event: InspectionRequestAssigned::class)]
class DispatchInspectionRequestNotifications implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(InspectionRequestCreated|InspectionRequestAssigned $event): void
    {
        $request = $event->request;
        $request->loadMissing(['requestType']);

        $assignee = User::find($request->assignee_id);
        if ($assignee) {
            $assignee->notify(new AssignedNotification($request));
        }

        $severity = $request->severity instanceof RequestSeverity
            ? $request->severity
            : RequestSeverity::from((string) $request->severity);

        $excluded = array_filter([
            $request->assignee_id,
            $request->requester_id,
            $event instanceof InspectionRequestAssigned ? $event->previousAssignee?->id : null,
        ]);

        if ($request->requestType) {
            $recipientIds = NotificationRecipientRule::query()
                ->matching($request->requestType, $severity)
                ->whereNotIn('recipient_user_id', $excluded)
                ->pluck('recipient_user_id')
                ->unique();

            if ($recipientIds->isNotEmpty()) {
                $recipients = User::whereIn('id', $recipientIds)->get();
                Notification::send($recipients, new AdditionalRecipientNotification($request));
            }
        }
    }
}
