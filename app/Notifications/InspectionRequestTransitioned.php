<?php

namespace App\Notifications;

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InspectionRequestTransitioned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly InspectionRequest $request,
        public readonly RequestStatus $from,
        public readonly RequestStatus $to,
        public readonly User $actor,
        public readonly ?string $note = null,
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
        $payload = NotificationPayload::forInspectionRequest($this->request, 'transitioned');
        $payload['from'] = ['value' => $this->from->value, 'label' => $this->from->label()];
        $payload['to'] = ['value' => $this->to->value, 'label' => $this->to->label()];
        $payload['actor_id'] = $this->actor->id;
        $payload['note'] = $this->note;

        return $payload;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $title = __('inspection_requests.transitioned.subject', ['title' => $this->request->title]);
        $lines = [
            __('inspection_requests.transitioned.line_1', [
                'actor' => $this->actor->name,
                'from' => $this->from->label(),
                'to' => $this->to->label(),
            ]),
        ];

        if ($this->note) {
            $lines[] = __('inspection_requests.transitioned.line_note', ['note' => $this->note]);
        }

        return (new MailMessage)
            ->subject($title)
            ->markdown('emails.inspection-requests.generic', [
                'title' => $this->request->title,
                'name' => $notifiable->name,
                'lines' => $lines,
                'actionUrl' => url('/dashboard/inspection-requests/'.$this->request->id),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toFcm(User $notifiable): array
    {
        return [
            'title' => __('inspection_requests.transitioned.subject', ['title' => $this->request->title]),
            'body' => $this->from->label().' → '.$this->to->label(),
            'data' => [
                'type' => 'inspection-request.transitioned',
                'request_id' => (string) $this->request->id,
                'from' => $this->from->value,
                'to' => $this->to->value,
            ],
        ];
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
