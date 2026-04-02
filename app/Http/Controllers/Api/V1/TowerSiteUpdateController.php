<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTowerSiteUpdateRequest;
use App\Http\Resources\TowerSiteUpdateResource;
use App\Models\TowerSiteUpdate;
use App\Models\TowerUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class TowerSiteUpdateController extends Controller
{
    public function index(TowerUnit $towerUnit): AnonymousResourceCollection
    {
        return TowerSiteUpdateResource::collection(
            $towerUnit->towerSiteUpdates()
                ->with('photos')
                ->latest('update_date')
                ->paginate(25)
        );
    }

    public function store(StoreTowerSiteUpdateRequest $request, TowerUnit $towerUnit): JsonResponse
    {
        $update = $towerUnit->towerSiteUpdates()->create(
            $request->safe()->only(['update_date', 'notes'])
        );

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $sortOrder => $file) {
                $path = Storage::disk('public')->put('site-updates/towers', $file);

                $update->photos()->create([
                    'photo_path' => $path,
                    'sort_order' => $sortOrder,
                ]);
            }
        }

        $update->load('photos');

        return (new TowerSiteUpdateResource($update))
            ->response()
            ->setStatusCode(201);
    }

    public function show(TowerSiteUpdate $update): TowerSiteUpdateResource
    {
        $update->load('photos');

        return new TowerSiteUpdateResource($update);
    }

    public function destroy(TowerSiteUpdate $update): JsonResponse
    {
        foreach ($update->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $update->delete();

        return response()->json(null, 204);
    }
}
