<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTowerTaskRequest;
use App\Http\Requests\UpdateTowerTaskRequest;
use App\Http\Resources\TowerTaskResource;
use App\Models\TowerTask;
use App\Models\TowerUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TowerTaskController extends Controller
{
    public function index(TowerUnit $towerUnit): AnonymousResourceCollection
    {
        return TowerTaskResource::collection(
            $towerUnit->towerTasks()->with('status')->paginate(25)
        );
    }

    public function store(StoreTowerTaskRequest $request, TowerUnit $towerUnit): JsonResponse
    {
        $task = $towerUnit->towerTasks()->create($request->validated());

        return (new TowerTaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(TowerTask $task): TowerTaskResource
    {
        $task->load('status');

        return new TowerTaskResource($task);
    }

    public function update(UpdateTowerTaskRequest $request, TowerTask $task): TowerTaskResource
    {
        $task->update($request->validated());

        return new TowerTaskResource($task);
    }

    public function destroy(TowerTask $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
