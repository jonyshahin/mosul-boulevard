<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class InspectionRequestService
{
    /** @var array<string, class-string<Model>> */
    private const SUBJECT_MAP = [
        'villa' => Villa::class,
        'tower_unit' => TowerUnit::class,
    ];

    public function __construct(
        private readonly ConnectionInterface $db,
        private readonly MediaUploadService $mediaUploadService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $uploadedFiles
     */
    public function create(array $data, User $author, array $uploadedFiles = []): InspectionRequest
    {
        return $this->db->transaction(function () use ($data, $author, $uploadedFiles) {
            $subject = $this->resolveSubject($data['subject_type'], (int) $data['subject_id']);

            $request = InspectionRequest::create([
                'requester_id' => $author->id,
                'assignee_id' => (int) $data['assignee_id'],
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
                'request_type_id' => (int) $data['request_type_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'location_detail' => $data['location_detail'] ?? null,
                'severity' => $data['severity'],
                'status' => RequestStatus::Open->value,
                'due_date' => $data['due_date'] ?? null,
            ]);

            foreach ($uploadedFiles as $file) {
                $this->mediaUploadService->store($file, $request, $author);
            }

            return $request;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(InspectionRequest $request, array $data): InspectionRequest
    {
        if (array_key_exists('subject_type', $data) && array_key_exists('subject_id', $data)) {
            $subject = $this->resolveSubject($data['subject_type'], (int) $data['subject_id']);
            $data['subject_type'] = $subject->getMorphClass();
            $data['subject_id'] = $subject->getKey();
        } else {
            unset($data['subject_type'], $data['subject_id']);
        }

        $request->update($data);

        return $request->fresh();
    }

    public function transition(
        InspectionRequest $request,
        RequestStatus $target,
        User $actor,
        ?string $note = null,
    ): InspectionRequest {
        return $this->db->transaction(function () use ($request, $target, $actor, $note) {
            $now = now();

            $updates = ['status' => $target->value];
            $updates = array_merge($updates, match ($target) {
                RequestStatus::Resolved => ['resolved_at' => $now],
                RequestStatus::Verified => ['verified_at' => $now, 'verified_by' => $actor->id],
                RequestStatus::Closed => ['closed_at' => $now],
                RequestStatus::Reopened => [
                    'resolved_at' => null,
                    'verified_at' => null,
                    'verified_by' => null,
                    'closed_at' => null,
                ],
                default => [],
            });

            $request->update($updates);

            if ($note !== null && $note !== '') {
                RequestReply::create([
                    'inspection_request_id' => $request->id,
                    'author_id' => $actor->id,
                    'body' => $note,
                    'triggers_status' => $target->value,
                ]);
            }

            return $request->fresh();
        });
    }

    public function assign(InspectionRequest $request, User $newAssignee, User $actor): InspectionRequest
    {
        $request->update(['assignee_id' => $newAssignee->id]);

        return $request->fresh();
    }

    public function destroy(InspectionRequest $request): void
    {
        $request->delete();
    }

    private function resolveSubject(string $token, int $id): Model
    {
        $class = self::SUBJECT_MAP[$token] ?? null;

        if ($class === null) {
            throw new InvalidArgumentException("Unknown subject type [{$token}]");
        }

        return $class::query()->findOrFail($id);
    }
}
