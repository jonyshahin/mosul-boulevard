<?php

namespace App\Notifications;

use App\Models\RequestReply;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class InspectionRequestRepliedTo extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly RequestReply $reply,
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
        $payload = NotificationPayload::forInspectionRequest($this->reply->request, 'replied');
        $payload['reply_id'] = $this->reply->id;
        $payload['actor_id'] = $this->reply->author_id;
        $payload['preview'] = Str::limit($this->reply->body, 140);

        return $payload;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $request = $this->reply->request;
        $actor = $this->reply->author;
        $title = __('inspection_requests.replied.subject', ['title' => $request->title]);

        return (new MailMessage)
            ->subject($title)
            ->markdown('emails.inspection-requests.generic', [
                'title' => $request->title,
                'name' => $notifiable->name,
                'lines' => [
                    __('inspection_requests.replied.line_1', ['actor' => $actor?->name ?? '-']),
                    __('inspection_requests.replied.line_preview', ['preview' => Str::limit($this->reply->body, 160)]),
                ],
                'actionUrl' => url('/dashboard/inspection-requests/'.$request->id),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toFcm(User $notifiable): array
    {
        return [
            'title' => __('inspection_requests.replied.subject', ['title' => $this->reply->request->title]),
            'body' => Str::limit($this->reply->body, 80),
            'data' => [
                'type' => 'inspection-request.replied',
                'request_id' => (string) $this->reply->inspection_request_id,
                'reply_id' => (string) $this->reply->id,
            ],
        ];
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
