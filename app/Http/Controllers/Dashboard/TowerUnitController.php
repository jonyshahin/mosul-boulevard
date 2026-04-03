<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTowerUnitRequest;
use App\Http\Requests\UpdateTowerUnitRequest;
use App\Models\ConstructionStage;
use App\Models\Engineer;
use App\Models\FloorDefinition;
use App\Models\StatusOption;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use Illuminate\Http\RedirectResponse;
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
            $query->where('code', 'like', '%'.$request->string('search').'%');
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

    public function create(): Response
    {
        return Inertia::render('dashboard/tower-units/Create', $this->lookupData());
    }

    public function store(StoreTowerUnitRequest $request): RedirectResponse
    {
        $towerUnit = TowerUnit::create($request->validated());

        return redirect()->route('dashboard.tower-units.show', $towerUnit)
            ->with('success', 'Tower unit created successfully.');
    }

    public function edit(TowerUnit $towerUnit): Response
    {
        return Inertia::render('dashboard/tower-units/Edit', [
            'towerUnit' => $towerUnit,
            ...$this->lookupData(),
        ]);
    }

    public function update(UpdateTowerUnitRequest $request, TowerUnit $towerUnit): RedirectResponse
    {
        $towerUnit->update($request->validated());

        return redirect()->route('dashboard.tower-units.show', $towerUnit)
            ->with('success', 'Tower unit updated successfully.');
    }

    public function destroy(TowerUnit $towerUnit): RedirectResponse
    {
        $towerUnit->delete();

        return redirect()->route('dashboard.tower-units.index')
            ->with('success', 'Tower unit deleted successfully.');
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

    /**
     * @return array<string, mixed>
     */
    private function lookupData(): array
    {
        return [
            'towerDefinitions' => TowerDefinition::all(),
            'floorDefinitions' => FloorDefinition::all(),
            'engineers' => Engineer::active()->get(),
            'stages' => ConstructionStage::forTowers()->ordered()->get(),
            'statuses' => StatusOption::forCategory('unit')->ordered()->get(),
            'structuralStatuses' => StatusOption::forCategory('structural')->ordered()->get(),
            'finishingStatuses' => StatusOption::forCategory('finishing')->ordered()->get(),
            'facadeStatuses' => StatusOption::forCategory('facade')->ordered()->get(),
        ];
    }
}
