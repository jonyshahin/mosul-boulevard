<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConstructionStage;
use App\Models\Engineer;
use App\Models\StatusOption;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VillaController extends Controller
{
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
            $query->where('code', 'like', '%' . $request->string('search') . '%');
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
}
