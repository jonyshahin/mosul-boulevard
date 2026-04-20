<?php

namespace App\Http\Resources;

use App\Enums\RequestSeverity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationRecipientRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $severity = $this->severity;
        if ($severity !== null && ! $severity instanceof RequestSeverity) {
            $severity = RequestSeverity::from($severity);
        }

        return [
            'id' => $this->id,
            'request_type' => new RequestTypeResource($this->whenLoaded('requestType')),
            'severity' => $severity
                ? ['value' => $severity->value, 'label' => $severity->label()]
                : null,
            'recipient' => new UserResource($this->whenLoaded('recipient')),
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
