<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        $contact = Setting::where('group', 'contact')
            ->pluck('value', 'key')
            ->toArray();

        return Inertia::render('website/Contact', [
            'contact' => $contact,
        ]);
    }
}
