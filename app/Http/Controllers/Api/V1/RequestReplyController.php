<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestReplyRequest;
use App\Http\Resources\RequestReplyResource;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Services\RequestReplyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RequestReplyController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly RequestReplyService $service,
    ) {}

    public function index(Request $request, InspectionRequest $inspectionRequest): AnonymousResourceCollection
    {
        $this->authorize('view', $inspectionRequest);

        $replies = $inspectionRequest->replies()
            ->with(['author', 'media.uploader'])
            ->paginate($this->perPage($request));

        return RequestReplyResource::collection($replies);
    }

    public function store(
        StoreRequestReplyRequest $request,
        InspectionRequest $inspectionRequest,
    ): JsonResponse {
        $this->authorize('create', [RequestReply::class, $inspectionRequest]);

        $files = $request->file('media', []);

        $reply = $this->service->create(
            $inspectionRequest,
            $request->validated(),
            $request->user(),
            is_array($files) ? $files : [],
        );

        $reply->load(['author', 'media.uploader']);

        return (new RequestReplyResource($reply))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(RequestReply $reply): JsonResponse
    {
        $this->authorize('delete', $reply);

        $this->service->destroy($reply);

        return response()->json(null, 204);
    }

    private function perPage(Request $request): int
    {
        $requested = (int) $request->input('per_page', 20);

        return max(1, min($requested, 100));
    }
}
