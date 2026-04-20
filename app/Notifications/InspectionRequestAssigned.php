<?php

namespace App\Notifications;

use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InspectionRequestAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly InspectionRequest $request,
    ) {
        $this->queue = config('inspection_requests.notification_queue');
    }

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        return NotificationPayload::forInspectionRequest($this->request, 'assigned');
    }

    public function toMail(User $notifiable): MailMessage
    {
        $title = __('inspection_requests.assigned.subject', ['title' => $this->request->title]);

        return (new MailMessage)
            ->subject($title)
            ->markdown('emails.inspection-requests.generic', [
                'title' => $this->request->title,
                'name' => $notifiable->name,
                'lines' => [
                    __('inspection_requests.assigned.line_1'),
                    __('inspection_requests.assigned.line_type', ['type' => $this->request->requestType?->name ?? '-']),
                    __('inspection_requests.assigned.line_severity', ['severity' => $this->request->severity?->label() ?? '-']),
                    __('inspection_requests.assigned.line_due', ['date' => $this->request->due_date?->toDateString() ?? '-']),
                ],
                'actionUrl' => url('/dashboard/inspection-requests/'.$this->request->id),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toFcm(User $notifiable): array
    {
        return [
            'title' => __('inspection_requests.assigned.subject', ['title' => $this->request->title]),
            'body' => $this->request->title,
            'data' => [
                'type' => 'inspection-request.assigned',
                'request_id' => (string) $this->request->id,
                'severity' => $this->request->severity?->value,
            ],
        ];
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'inspection-request.assigned',
            'request' => NotificationPayload::forInspectionRequest($this->request, 'assigned'),
        ]);
    }
}
