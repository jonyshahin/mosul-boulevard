<?php

namespace App\Notifications;

use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InspectionRequestOverdueReminder extends Notification implements ShouldQueue
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
        return NotificationPayload::forInspectionRequest($this->request, 'overdue');
    }

    public function toMail(User $notifiable): MailMessage
    {
        $title = __('inspection_requests.overdue.subject', ['title' => $this->request->title]);

        return (new MailMessage)
            ->subject($title)
            ->markdown('emails.inspection-requests.generic', [
                'title' => $this->request->title,
                'name' => $notifiable->name,
                'lines' => [
                    __('inspection_requests.overdue.line_1'),
                    __('inspection_requests.overdue.line_due', ['date' => $this->request->due_date?->toDateString() ?? '-']),
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
            'title' => __('inspection_requests.overdue.subject', ['title' => $this->request->title]),
            'body' => __('inspection_requests.overdue.line_1'),
            'data' => [
                'type' => 'inspection-request.overdue',
                'request_id' => (string) $this->request->id,
            ],
        ];
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
