<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVillaTaskRequest;
use App\Http\Requests\UpdateVillaTaskRequest;
use App\Http\Resources\VillaTaskResource;
use App\Models\Villa;
use App\Models\VillaTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VillaTaskController extends Controller
{
    public function index(Villa $villa): AnonymousResourceCollection
    {
        return VillaTaskResource::collection(
            $villa->villaTasks()->with('status')->paginate(25)
        );
    }

    public function store(StoreVillaTaskRequest $request, Villa $villa): JsonResponse
    {
        $task = $villa->villaTasks()->create($request->validated());

        return (new VillaTaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VillaTask $task): VillaTaskResource
    {
        $task->load('status');

        return new VillaTaskResource($task);
    }

    public function update(UpdateVillaTaskRequest $request, VillaTask $task): VillaTaskResource
    {
        $task->update($request->validated());

        return new VillaTaskResource($task);
    }

    public function destroy(VillaTask $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
