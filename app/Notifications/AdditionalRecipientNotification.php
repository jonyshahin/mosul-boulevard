<?php

namespace App\Notifications;

use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdditionalRecipientNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly InspectionRequest $request,
        public readonly string $reason = 'rule-match',
    ) {
        $this->connection = 'redis';
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
        $payload = NotificationPayload::forInspectionRequest($this->request, 'additional-recipient');
        $payload['reason'] = $this->reason;

        return $payload;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $title = __('inspection_requests.additional.subject', ['title' => $this->request->title]);

        return (new MailMessage)
            ->subject($title)
            ->markdown('emails.inspection-requests.generic', [
                'title' => $this->request->title,
                'name' => $notifiable->name,
                'lines' => [
                    __('inspection_requests.additional.line_1'),
                    __('inspection_requests.additional.line_type', ['type' => $this->request->requestType?->name ?? '-']),
                    __('inspection_requests.additional.line_severity', ['severity' => $this->request->severity?->label() ?? '-']),
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
            'title' => __('inspection_requests.additional.subject', ['title' => $this->request->title]),
            'body' => $this->request->title,
            'data' => [
                'type' => 'inspection-request.additional-recipient',
                'request_id' => (string) $this->request->id,
                'reason' => $this->reason,
            ],
        ];
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
