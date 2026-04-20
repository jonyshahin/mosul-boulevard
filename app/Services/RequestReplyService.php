<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Events\RequestReplyCreated;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RequestReplyService
{
    public function __construct(
        private readonly ConnectionInterface $db,
        private readonly MediaUploadService $mediaUploadService,
        private readonly InspectionRequestService $inspectionRequestService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $uploadedFiles
     */
    public function create(
        InspectionRequest $request,
        array $data,
        User $author,
        array $uploadedFiles = [],
    ): RequestReply {
        return $this->db->transaction(function () use ($request, $data, $author, $uploadedFiles) {
            $triggers = isset($data['triggers_status']) && $data['triggers_status'] !== ''
                ? RequestStatus::from($data['triggers_status'])
                : null;

            $reply = RequestReply::create([
                'inspection_request_id' => $request->id,
                'author_id' => $author->id,
                'body' => $data['body'],
                'triggers_status' => $triggers?->value,
            ]);

            foreach ($uploadedFiles as $file) {
                $this->mediaUploadService->store($file, $reply, $author);
            }

            if ($triggers !== null) {
                $this->inspectionRequestService->transition($request, $triggers, $author);
            }

            DB::afterCommit(function () use ($reply): void {
                RequestReplyCreated::dispatch($reply->fresh());
            });

            return $reply;
        });
    }

    public function destroy(RequestReply $reply): void
    {
        $reply->delete();
    }
}
