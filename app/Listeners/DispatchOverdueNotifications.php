<?php

namespace App\Listeners;

use App\Enums\RequestSeverity;
use App\Events\InspectionRequestOverdue;
use App\Models\NotificationRecipientRule;
use App\Models\User;
use App\Notifications\AdditionalRecipientNotification;
use App\Notifications\InspectionRequestOverdueReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\AsEventListener;
use Illuminate\Support\Facades\Notification;

#[AsEventListener(event: InspectionRequestOverdue::class)]
class DispatchOverdueNotifications implements ShouldQueue
{

    public string $queue = 'notifications';

    public function handle(InspectionRequestOverdue $event): void
    {
        $request = $event->request;
        $request->loadMissing('requestType');

        if ($assignee = User::find($request->assignee_id)) {
            $assignee->notify(new InspectionRequestOverdueReminder($request));
        }

        $severity = $request->severity instanceof RequestSeverity
            ? $request->severity
            : RequestSeverity::from((string) $request->severity);

        if ($request->requestType) {
            $excluded = array_filter([$request->assignee_id, $request->requester_id]);

            $recipientIds = NotificationRecipientRule::query()
                ->matching($request->requestType, $severity)
                ->whereNotIn('recipient_user_id', $excluded)
                ->pluck('recipient_user_id')
                ->unique();

            if ($recipientIds->isNotEmpty()) {
                $recipients = User::whereIn('id', $recipientIds)->get();
                Notification::send($recipients, new AdditionalRecipientNotification(
                    request: $request,
                    reason: 'overdue',
                ));
            }
        }
    }
}
