<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($request->only(['name', 'email', 'subject', 'message']));

        return redirect()->route('website.contact')
            ->with('success', 'Your message has been sent! We will get back to you soon.');
    }
}
