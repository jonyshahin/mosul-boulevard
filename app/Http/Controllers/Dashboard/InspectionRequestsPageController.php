<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InspectionRequestsPageController extends Controller
{
    public function index(Request $request): Response
    {
        $this->denyCustomers($request);

        return Inertia::render('dashboard/inspection-requests/Index', [
            'translations' => [
                'title' => __('inspection_requests.pages.index.title'),
                'coming_soon' => __('inspection_requests.pages.coming_soon'),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->denyCustomers($request);

        return Inertia::render('dashboard/inspection-requests/Create', [
            'translations' => [
                'title' => __('inspection_requests.pages.create.title'),
                'coming_soon' => __('inspection_requests.pages.coming_soon'),
            ],
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $this->denyCustomers($request);

        return Inertia::render('dashboard/inspection-requests/Show', [
            'id' => $id,
            'translations' => [
                'title' => __('inspection_requests.pages.show.title', ['id' => $id]),
                'coming_soon' => __('inspection_requests.pages.coming_soon'),
            ],
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $this->denyCustomers($request);

        return Inertia::render('dashboard/inspection-requests/Edit', [
            'id' => $id,
            'translations' => [
                'title' => __('inspection_requests.pages.edit.title'),
                'coming_soon' => __('inspection_requests.pages.coming_soon'),
            ],
        ]);
    }

    private function denyCustomers(Request $request): void
    {
        abort_if($request->user()?->isCustomer() ?? true, 403);
    }
}
