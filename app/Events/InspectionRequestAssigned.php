<?php

namespace App\Events;

use App\Models\InspectionRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InspectionRequestAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly InspectionRequest $request,
        public readonly ?User $previousAssignee = null,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('inspection-requests.'.$this->request->id)];

        $users = array_filter([
            $this->request->assignee_id,
            $this->previousAssignee?->id,
        ]);

        foreach (array_unique($users) as $uid) {
            $channels[] = new PrivateChannel('users.'.$uid.'.notifications');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'inspection-request.assigned';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $payload = InspectionRequestEventPayload::make($this->request);
        $payload['previous_assignee'] = $this->previousAssignee
            ? ['id' => $this->previousAssignee->id, 'name' => $this->previousAssignee->name]
            : null;

        return $payload;
    }
}
