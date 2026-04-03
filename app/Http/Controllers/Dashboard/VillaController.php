<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVillaRequest;
use App\Http\Requests\UpdateVillaRequest;
use App\Models\ConstructionStage;
use App\Models\Engineer;
use App\Models\StatusOption;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VillaController extends Controller
{
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

        return Inertia::render('dashboard/villas/Show', [
            'villa' => $villa,
            'stages' => ConstructionStage::forVillas()->ordered()->get(),
            'statuses' => StatusOption::forCategory('unit')->ordered()->get(),
            'structuralStatuses' => StatusOption::forCategory('structural')->ordered()->get(),
            'finishingStatuses' => StatusOption::forCategory('finishing')->ordered()->get(),
            'facadeStatuses' => StatusOption::forCategory('facade')->ordered()->get(),
            'engineers' => Engineer::active()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/villas/Create', $this->lookupData());
    }

    public function store(StoreVillaRequest $request): RedirectResponse
    {
        $villa = Villa::create($request->validated());

        return redirect()->route('dashboard.villas.show', $villa)
            ->with('success', 'Villa created successfully.');
    }

    public function edit(Villa $villa): Response
    {
        return Inertia::render('dashboard/villas/Edit', [
            'villa' => $villa,
            ...$this->lookupData(),
        ]);
    }

    public function update(UpdateVillaRequest $request, Villa $villa): RedirectResponse
    {
        $villa->update($request->validated());

        return redirect()->route('dashboard.villas.show', $villa)
            ->with('success', 'Villa updated successfully.');
    }

    public function destroy(Villa $villa): RedirectResponse
    {
        $villa->delete();

        return redirect()->route('dashboard.villas.index')
            ->with('success', 'Villa deleted successfully.');
    }

    public function index(Request $request): Response
    {
        $query = Villa::filter($request->only([
            'villa_type_id',
            'is_sold',
            'status_option_id',
            'engineer_id',
            'current_stage_id',
        ]));

        if ($request->filled('search')) {
            $query->where('code', 'like', '%'.$request->string('search').'%');
        }

        $villas = $query
            ->with([
                'villaType',
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

        return Inertia::render('dashboard/villas/Index', [
            'villas' => $villas,
            'villaTypes' => VillaType::all(),
            'engineers' => Engineer::active()->get(),
            'stages' => ConstructionStage::forVillas()->ordered()->get(),
            'statuses' => StatusOption::forCategory('unit')->ordered()->get(),
            'filters' => $request->only([
                'search',
                'villa_type_id',
                'is_sold',
                'status_option_id',
                'engineer_id',
                'current_stage_id',
            ]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function lookupData(): array
    {
        return [
            'villaTypes' => VillaType::all(),
            'engineers' => Engineer::active()->get(),
            'stages' => ConstructionStage::forVillas()->ordered()->get(),
            'statuses' => StatusOption::forCategory('unit')->ordered()->get(),
            'structuralStatuses' => StatusOption::forCategory('structural')->ordered()->get(),
            'finishingStatuses' => StatusOption::forCategory('finishing')->ordered()->get(),
            'facadeStatuses' => StatusOption::forCategory('facade')->ordered()->get(),
        ];
    }
}
