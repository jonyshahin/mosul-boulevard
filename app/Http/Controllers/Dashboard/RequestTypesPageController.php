<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RequestTypesPageController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin() ?? false, 403);

        return Inertia::render('dashboard/settings/request-types/Index', [
            'translations' => [
                'title' => __('settings.request_types.page.title'),
                'coming_soon' => __('settings.request_types.page.coming_soon'),
            ],
        ]);
    }
}
