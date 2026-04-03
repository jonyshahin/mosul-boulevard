<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\Villa;
use App\Models\VillaType;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('website/Home', [
            'stats' => [
                'total_villas' => Villa::count(),
                'total_tower_units' => TowerUnit::count(),
                'villas_sold' => Villa::where('is_sold', true)->count(),
                'tower_units_sold' => TowerUnit::where('is_sold', true)->count(),
            ],
            'villaTypes' => VillaType::all(),
            'towerDefinitions' => TowerDefinition::all(),
        ]);
    }
}
