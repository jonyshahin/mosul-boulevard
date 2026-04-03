<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use App\Models\TowerTask;
use App\Models\TowerUnit;
use App\Models\Villa;
use App\Models\VillaTask;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('dashboard/Index', [
            'stats' => [
                'total_villas' => Villa::count(),
                'total_tower_units' => TowerUnit::count(),
                'villas_sold' => Villa::where('is_sold', true)->count(),
                'tower_units_sold' => TowerUnit::where('is_sold', true)->count(),
                'total_engineers' => Engineer::where('is_active', true)->count(),
                'total_villa_tasks' => VillaTask::count(),
                'total_tower_tasks' => TowerTask::count(),
            ],
            'salesChart' => [
                'villas' => DB::table('vw_villas_sales_summary')->get(),
                'towers' => DB::table('vw_towers_sales_summary')->get(),
            ],
            'structuralChart' => [
                'villas' => DB::table('vw_villas_structural_status')->get(),
            ],
        ]);
    }
}
