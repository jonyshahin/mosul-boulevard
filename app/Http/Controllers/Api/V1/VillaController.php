<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVillaRequest;
use App\Http\Requests\UpdateVillaRequest;
use App\Http\Resources\VillaResource;
use App\Models\Villa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VillaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'villa_type_id',
            'is_sold',
            'status_option_id',
            'engineer_id',
            'current_stage_id',
            'structural_status_id',
            'finishing_status_id',
            'facade_status_id',
        ]);

        $query = Villa::filter($filters)
            ->with([
                'villaType',
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

        return VillaResource::collection($query->paginate(25));
    }

    public function show(Villa $villa): VillaResource
    {
        $villa->load([
            'villaType',
            'currentStage',
            'status',
            'engineer',
            'structuralStatus',
            'finishingStatus',
            'facadeStatus',
            'villaTasks.status',
            'villaSiteUpdates.photos',
        ]);

        return new VillaResource($villa);
    }

    public function store(StoreVillaRequest $request): JsonResponse
    {
        $villa = Villa::create($request->validated());

        return (new VillaResource($villa))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateVillaRequest $request, Villa $villa): VillaResource
    {
        $villa->update($request->validated());

        return new VillaResource($villa);
    }

    public function destroy(Villa $villa): JsonResponse
    {
        $villa->delete();

        return response()->json(null, 204);
    }
}
