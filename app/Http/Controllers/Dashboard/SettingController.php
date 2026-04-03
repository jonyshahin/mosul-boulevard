<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::where('group', 'contact')
            ->pluck('value', 'key')
            ->toArray();

        return Inertia::render('dashboard/settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_phone' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'string', 'max:500'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_whatsapp' => ['nullable', 'string', 'max:500'],
            'contact_working_hours' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', 'contact');
        }

        return redirect()->route('dashboard.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
