<?php

namespace App\Events;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Database\Eloquent\Model;

final class InspectionRequestEventPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function make(InspectionRequest $request, ?User $actor = null): array
    {
        $severity = $request->severity instanceof RequestSeverity
            ? $request->severity
            : RequestSeverity::from((string) $request->severity);

        $status = $request->status instanceof RequestStatus
            ? $request->status
            : RequestStatus::from((string) $request->status);

        return [
            'id' => $request->id,
            'title' => $request->title,
            'severity' => ['value' => $severity->value, 'label' => $severity->label()],
            'status' => ['value' => $status->value, 'label' => $status->label()],
            'subject' => self::subjectSummary($request),
            'due_date' => $request->due_date?->toDateString(),
            'actor' => $actor ? ['id' => $actor->id, 'name' => $actor->name] : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reply(RequestReply $reply): array
    {
        $payload = self::make($reply->request, $reply->author);
        $payload['reply'] = [
            'id' => $reply->id,
            'body' => $reply->body,
            'triggers_status' => $reply->triggers_status?->value,
        ];

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function subjectSummary(InspectionRequest $request): ?array
    {
        /** @var Model|null $subject */
        $subject = $request->subject;

        if (! $subject) {
            return null;
        }

        return [
            'type' => match (true) {
                $subject instanceof Villa => 'villa',
                $subject instanceof TowerUnit => 'tower_unit',
                default => $subject->getMorphClass(),
            },
            'id' => $subject->getKey(),
            'code' => $subject->code ?? null,
        ];
    }
}
