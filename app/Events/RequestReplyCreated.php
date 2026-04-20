<?php

namespace App\Events;

use App\Models\RequestReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestReplyCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly RequestReply $reply,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        $request = $this->reply->request;
        $channels = [new PrivateChannel('inspection-requests.'.$request->id)];

        foreach (array_unique(array_filter([$request->assignee_id, $request->requester_id])) as $uid) {
            $channels[] = new PrivateChannel('users.'.$uid.'.notifications');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'inspection-request.reply-created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return InspectionRequestEventPayload::reply($this->reply);
    }
}
