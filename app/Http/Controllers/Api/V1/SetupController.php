<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConstructionStageResource;
use App\Http\Resources\EngineerResource;
use App\Http\Resources\StatusOptionResource;
use App\Models\ConstructionStage;
use App\Models\Engineer;
use App\Models\StatusOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SetupController extends Controller
{
    public function stages(Request $request): AnonymousResourceCollection
    {
        $query = ConstructionStage::active()->ordered();

        if ($request->filled('property_type')) {
            $propertyTypeId = $request->input('property_type') === 'villas' ? 1 : 2;
            $query->where('property_type_id', $propertyTypeId);
        }

        return ConstructionStageResource::collection($query->get());
    }

    public function statuses(Request $request): AnonymousResourceCollection
    {
        $query = StatusOption::active()->ordered();

        if ($request->filled('category')) {
            $query->forCategory($request->input('category'));
        }

        return StatusOptionResource::collection($query->get());
    }

    public function engineers(): AnonymousResourceCollection
    {
        return EngineerResource::collection(Engineer::active()->get());
    }
}
