<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConstructionStage;
use App\Models\Engineer;
use App\Models\StatusOption;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TowerUnitController extends Controller
{
    public function index(Request $request): Response
    {
        $query = TowerUnit::filter($request->only([
            'tower_definition_id',
            'floor_definition_id',
            'is_sold',
            'status_option_id',
            'engineer_id',
            'current_stage_id',
        ]));

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->string('search') . '%');
        }

        $towerUnits = $query
            ->with([
                'towerDefinition',
                'floorDefinition',
                'currentStage',
                'status',
                'engineer',
                'structuralStatus',
                'finishingStatus',
                'facadeStatus',
            ])
            ->orderBy('code')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('dashboard/tower-units/Index', [
            'towerUnits' => $towerUnits,
            'towerDefinitions' => TowerDefinition::all(),
            'engineers' => Engineer::active()->get(),
            'stages' => ConstructionStage::forTowers()->ordered()->get(),
            'statuses' => StatusOption::forCategory('unit')->ordered()->get(),
            'filters' => $request->only([
                'search',
                'tower_definition_id',
                'floor_definition_id',
                'is_sold',
                'status_option_id',
                'engineer_id',
                'current_stage_id',
            ]),
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

        return Inertia::render('dashboard/tower-units/Show', [
            'towerUnit' => $towerUnit,
            'stages' => ConstructionStage::forTowers()->ordered()->get(),
            'statuses' => StatusOption::forCategory('unit')->ordered()->get(),
            'structuralStatuses' => StatusOption::forCategory('structural')->ordered()->get(),
            'finishingStatuses' => StatusOption::forCategory('finishing')->ordered()->get(),
            'facadeStatuses' => StatusOption::forCategory('facade')->ordered()->get(),
            'engineers' => Engineer::active()->get(),
        ]);
    }
}
