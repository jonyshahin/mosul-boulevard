<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestTypeRequest;
use App\Http\Requests\UpdateRequestTypeRequest;
use App\Http\Resources\RequestTypeResource;
use App\Models\RequestType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RequestTypeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', RequestType::class);

        $query = RequestType::query()->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        return RequestTypeResource::collection($query->paginate($this->perPage($request)));
    }

    public function store(StoreRequestTypeRequest $request): JsonResponse
    {
        $this->authorize('create', RequestType::class);

        $type = RequestType::create($request->validated());

        return (new RequestTypeResource($type))
            ->response()
            ->setStatusCode(201);
    }

    public function show(RequestType $requestType): RequestTypeResource
    {
        $this->authorize('view', $requestType);

        return new RequestTypeResource($requestType);
    }

    public function update(UpdateRequestTypeRequest $request, RequestType $requestType): RequestTypeResource
    {
        $this->authorize('update', $requestType);

        $requestType->update($request->validated());

        return new RequestTypeResource($requestType);
    }

    public function destroy(RequestType $requestType): JsonResponse
    {
        $this->authorize('delete', $requestType);

        $requestType->delete();

        return response()->json(null, 204);
    }

    private function perPage(Request $request): int
    {
        $requested = (int) $request->input('per_page', 20);

        return max(1, min($requested, 100));
    }
}
