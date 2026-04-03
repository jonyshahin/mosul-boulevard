<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TowerUnitController extends Controller
{
    public function index(Request $request): Response
    {
        $query = TowerUnit::query();

        if ($request->filled('tower_definition_id')) {
            $query->where('tower_definition_id', $request->input('tower_definition_id'));
        }

        $towerUnits = $query
            ->with([
                'towerDefinition',
                'floorDefinition',
                'currentStage',
                'status',
                'structuralStatus',
                'finishingStatus',
                'facadeStatus',
            ])
            ->orderBy('code')
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('website/towers/Index', [
            'towerUnits' => $towerUnits,
            'towerDefinitions' => TowerDefinition::all(),
            'filters' => $request->only(['tower_definition_id']),
        ]);
    }

    public function show(TowerUnit $towerUnit): Response
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

        return Inertia::render('website/towers/Show', [
            'towerUnit' => $towerUnit,
        ]);
    }
}
