<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConstructionStage;
use App\Models\Engineer;
use App\Models\PropertyType;
use App\Models\StatusOption;
use Inertia\Inertia;
use Inertia\Response;

class SetupController extends Controller
{
    public function stages(): Response
    {
        return Inertia::render('dashboard/setup/Stages', [
            'stages' => ConstructionStage::with('propertyType')->ordered()->get(),
            'propertyTypes' => PropertyType::all(),
        ]);
    }

    public function statuses(): Response
    {
        return Inertia::render('dashboard/setup/Statuses', [
            'statuses' => StatusOption::ordered()->get(),
        ]);
    }

    public function engineers(): Response
    {
        return Inertia::render('dashboard/setup/Engineers', [
            'engineers' => Engineer::withTrashed()->get(),
        ]);
    }
}
