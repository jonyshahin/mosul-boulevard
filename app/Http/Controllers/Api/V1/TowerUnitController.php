<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTowerUnitRequest;
use App\Http\Requests\UpdateTowerUnitRequest;
use App\Http\Resources\TowerUnitResource;
use App\Models\TowerUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TowerUnitController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'tower_definition_id',
            'floor_definition_id',
            'is_sold',
            'status_option_id',
            'engineer_id',
            'current_stage_id',
            'structural_status_id',
            'finishing_status_id',
            'facade_status_id',
        ]);

        $query = TowerUnit::filter($filters)
            ->with([
                'towerDefinition',
                'floorDefinition',
                'currentStage',
                'status',
                'engineer',
                'structuralStatus',
                'finishingStatus',
                'facadeStatus',
            ]);

        if ($request->filled('search')) {
            $query->where('code', 'like', '%'.$request->input('search').'%');
        }

        return TowerUnitResource::collection($query->paginate(25));
    }

    public function show(TowerUnit $towerUnit): TowerUnitResource
    {
        $towerUnit->load([
            'towerDefinition',
            'floorDefinition',
            'currentStage',
            'status',
            'engineer',
            'structuralStatus',
            'finishingStatus',
            'facadeStatus',
            'towerTasks.status',
            'towerSiteUpdates.photos',
        ]);

        return new TowerUnitResource($towerUnit);
    }

    public function store(StoreTowerUnitRequest $request): JsonResponse
    {
        $towerUnit = TowerUnit::create($request->validated());

        return (new TowerUnitResource($towerUnit))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateTowerUnitRequest $request, TowerUnit $towerUnit): TowerUnitResource
    {
        $towerUnit->update($request->validated());

        return new TowerUnitResource($towerUnit);
    }

    public function destroy(TowerUnit $towerUnit): JsonResponse
    {
        $towerUnit->delete();

        return response()->json(null, 204);
    }
}
