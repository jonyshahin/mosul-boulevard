<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TowerSiteUpdate;
use App\Models\TowerTask;
use App\Models\TowerUnit;
use App\Models\Villa;
use App\Models\VillaSiteUpdate;
use App\Models\VillaTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesSummary(Request $request): JsonResponse
    {
        return response()->json([
            'villas' => DB::table('vw_villas_sales_summary')->get(),
            'towers' => DB::table('vw_towers_sales_summary')->get(),
        ]);
    }

    public function structuralStatus(Request $request): JsonResponse
    {
        return response()->json([
            'villas' => DB::table('vw_villas_structural_status')->get(),
            'towers' => DB::table('vw_towers_structural_status')->get(),
        ]);
    }

    public function finishingStatus(Request $request): JsonResponse
    {
        return response()->json([
            'villas' => DB::table('vw_villas_finishing_status')->get(),
            'towers' => DB::table('vw_towers_finishing_status')->get(),
        ]);
    }

    public function facadeStatus(Request $request): JsonResponse
    {
        return response()->json([
            'villas' => DB::table('vw_villas_facade_status')->get(),
            'towers' => DB::table('vw_towers_facade_status')->get(),
        ]);
    }

    public function dashboardStats(Request $request): JsonResponse
    {
        $totalVillas = Villa::count();
        $totalTowerUnits = TowerUnit::count();
        $villasSold = Villa::where('is_sold', true)->count();
        $towerUnitsSold = TowerUnit::where('is_sold', true)->count();

        return response()->json([
            'total_villas' => $totalVillas,
            'total_tower_units' => $totalTowerUnits,
            'villas_sold' => $villasSold,
            'tower_units_sold' => $towerUnitsSold,
            'villas_sold_pct' => $totalVillas > 0 ? round(($villasSold / $totalVillas) * 100, 2) : 0,
            'tower_units_sold_pct' => $totalTowerUnits > 0 ? round(($towerUnitsSold / $totalTowerUnits) * 100, 2) : 0,
            'total_villa_tasks' => VillaTask::count(),
            'total_tower_tasks' => TowerTask::count(),
            'recent_villa_updates' => VillaSiteUpdate::latest('update_date')->take(5)->with('villa:id,code')->get(),
            'recent_tower_updates' => TowerSiteUpdate::latest('update_date')->take(5)->with('towerUnit:id,code')->get(),
        ]);
    }
}
