<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(): Response
    {
        $messages = ContactMessage::latest()
            ->paginate(25);

        $unreadCount = ContactMessage::unread()->count();

        return Inertia::render('dashboard/messages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function show(ContactMessage $contactMessage): Response
    {
        if (! $contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return Inertia::render('dashboard/messages/Show', [
            'message' => $contactMessage,
        ]);
    }

    public function reply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $request->validate([
            'admin_reply' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage->update([
            'admin_reply' => $request->input('admin_reply'),
            'replied_at' => now(),
        ]);

        return redirect()->route('dashboard.messages.show', $contactMessage)
            ->with('success', 'Reply saved successfully.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('dashboard.messages.index')
            ->with('success', 'Message deleted.');
    }
}
