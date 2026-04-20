<?php

namespace App\Events;

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InspectionRequestTransitioned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly InspectionRequest $request,
        public readonly RequestStatus $from,
        public readonly RequestStatus $to,
        public readonly User $actor,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('inspection-requests.'.$this->request->id)];

        foreach (array_unique(array_filter([$this->request->assignee_id, $this->request->requester_id])) as $uid) {
            $channels[] = new PrivateChannel('users.'.$uid.'.notifications');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'inspection-request.transitioned';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $payload = InspectionRequestEventPayload::make($this->request, $this->actor);
        $payload['transition'] = [
            'from' => ['value' => $this->from->value, 'label' => $this->from->label()],
            'to' => ['value' => $this->to->value, 'label' => $this->to->label()],
        ];

        return $payload;
    }
}
