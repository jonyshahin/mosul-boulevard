<?php

namespace App\Http\Resources;

use App\Enums\RequestStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestReplyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'triggers_status' => $this->formatTriggers(),
            'author' => new UserResource($this->whenLoaded('author')),
            'media' => RequestMediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * @return array<string, string>|null
     */
    private function formatTriggers(): ?array
    {
        if (! $this->triggers_status) {
            return null;
        }

        $status = $this->triggers_status instanceof RequestStatus
            ? $this->triggers_status
            : RequestStatus::from($this->triggers_status);

        return [
            'value' => $status->value,
            'label' => $status->label(),
        ];
    }
}
