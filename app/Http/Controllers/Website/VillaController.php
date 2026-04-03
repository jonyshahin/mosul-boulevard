<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VillaController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Villa::query();

        if ($request->filled('villa_type_id')) {
            $query->where('villa_type_id', $request->input('villa_type_id'));
        }

        $villas = $query
            ->with([
                'villaType',
                'currentStage',
                'status',
                'structuralStatus',
                'finishingStatus',
                'facadeStatus',
            ])
            ->orderBy('code')
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('website/villas/Index', [
            'villas' => $villas,
            'villaTypes' => VillaType::all(),
            'filters' => $request->only(['villa_type_id']),
        ]);
    }

    public function show(Villa $villa): Response
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

        return Inertia::render('website/villas/Show', [
            'villa' => $villa,
        ]);
    }
}
