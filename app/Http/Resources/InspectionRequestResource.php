<?php

namespace App\Http\Resources;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\TowerUnit;
use App\Models\Villa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $severity = $this->severity instanceof RequestSeverity
            ? $this->severity
            : RequestSeverity::from($this->severity);

        $status = $this->status instanceof RequestStatus
            ? $this->status
            : RequestStatus::from($this->status);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'location_detail' => $this->location_detail,
            'severity' => [
                'value' => $severity->value,
                'label' => $severity->label(),
                'color' => $severity->color(),
            ],
            'status' => [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
            ],
            'due_date' => $this->due_date?->toDateString(),
            'is_overdue' => $this->isOverdue($status),
            'resolved_at' => $this->resolved_at,
            'verified_at' => $this->verified_at,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'requester' => new UserResource($this->whenLoaded('requester')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'verified_by' => new UserResource($this->whenLoaded('verifiedBy')),
            'request_type' => new RequestTypeResource($this->whenLoaded('requestType')),
            'subject' => $this->formatSubject(),
            'replies_count' => $this->whenCounted('replies'),
            'media_count' => $this->whenCounted('media'),
        ];
    }

    private function isOverdue(RequestStatus $status): bool
    {
        if (! $this->due_date) {
            return false;
        }

        if (in_array($status, [RequestStatus::Verified, RequestStatus::Closed], true)) {
            return false;
        }

        return $this->due_date->lt(now());
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatSubject(): ?array
    {
        /** @var Model|null $subject */
        $subject = $this->whenLoaded('subject', fn () => $this->subject, fn () => null);

        if (! $subject) {
            return null;
        }

        return [
            'type' => $this->subjectTypeKey($subject),
            'id' => $subject->getKey(),
            'code' => $subject->code ?? null,
            'display_name' => $subject->code ?? (string) $subject->getKey(),
        ];
    }

    private function subjectTypeKey(Model $subject): string
    {
        return match (true) {
            $subject instanceof Villa => 'villa',
            $subject instanceof TowerUnit => 'tower_unit',
            default => $subject->getMorphClass(),
        };
    }
}
