<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVillaSiteUpdateRequest;
use App\Http\Resources\VillaSiteUpdateResource;
use App\Models\Villa;
use App\Models\VillaSiteUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class VillaSiteUpdateController extends Controller
{
    public function index(Villa $villa): AnonymousResourceCollection
    {
        return VillaSiteUpdateResource::collection(
            $villa->villaSiteUpdates()
                ->with('photos')
                ->latest('update_date')
                ->paginate(25)
        );
    }

    public function store(StoreVillaSiteUpdateRequest $request, Villa $villa): JsonResponse
    {
        $update = $villa->villaSiteUpdates()->create(
            $request->safe()->only(['update_date', 'notes'])
        );

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $sortOrder => $file) {
                $path = Storage::disk('public')->put('site-updates/villas', $file);

                $update->photos()->create([
                    'photo_path' => $path,
                    'sort_order' => $sortOrder,
                ]);
            }
        }

        $update->load('photos');

        return (new VillaSiteUpdateResource($update))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VillaSiteUpdate $update): VillaSiteUpdateResource
    {
        $update->load('photos');

        return new VillaSiteUpdateResource($update);
    }

    public function destroy(VillaSiteUpdate $update): JsonResponse
    {
        foreach ($update->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $update->delete();

        return response()->json(null, 204);
    }
}
