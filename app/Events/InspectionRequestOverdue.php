<?php

namespace App\Events;

use App\Models\InspectionRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InspectionRequestOverdue implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly InspectionRequest $request,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('inspection-requests.'.$this->request->id)];

        if ($this->request->assignee_id) {
            $channels[] = new PrivateChannel('users.'.$this->request->assignee_id.'.notifications');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'inspection-request.overdue';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return InspectionRequestEventPayload::make($this->request);
    }
}
