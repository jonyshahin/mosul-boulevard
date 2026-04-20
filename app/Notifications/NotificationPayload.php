<?php

namespace App\Notifications;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;

final class NotificationPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function forInspectionRequest(InspectionRequest $request, string $event): array
    {
        $severity = $request->severity instanceof RequestSeverity
            ? $request->severity
            : RequestSeverity::from((string) $request->severity);

        $status = $request->status instanceof RequestStatus
            ? $request->status
            : RequestStatus::from((string) $request->status);

        return [
            'event' => $event,
            'id' => $request->id,
            'title' => $request->title,
            'severity' => ['value' => $severity->value, 'label' => $severity->label()],
            'status' => ['value' => $status->value, 'label' => $status->label()],
            'due_date' => $request->due_date?->toDateString(),
            'request_type_id' => $request->request_type_id,
            'subject_type' => $request->subject_type,
            'subject_id' => $request->subject_id,
        ];
    }
}
