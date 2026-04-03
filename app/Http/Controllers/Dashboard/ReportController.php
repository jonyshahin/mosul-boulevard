<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('dashboard/reports/Index', [
            'salesVillas' => DB::table('vw_villas_sales_summary')->get(),
            'salesTowers' => DB::table('vw_towers_sales_summary')->get(),
            'structuralVillas' => DB::table('vw_villas_structural_status')->get(),
            'structuralTowers' => DB::table('vw_towers_structural_status')->get(),
            'finishingVillas' => DB::table('vw_villas_finishing_status')->get(),
            'finishingTowers' => DB::table('vw_towers_finishing_status')->get(),
            'facadeVillas' => DB::table('vw_villas_facade_status')->get(),
            'facadeTowers' => DB::table('vw_towers_facade_status')->get(),
        ]);
    }
}
