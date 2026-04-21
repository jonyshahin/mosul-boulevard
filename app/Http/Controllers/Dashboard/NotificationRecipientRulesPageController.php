<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationRecipientRulesPageController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin() ?? false, 403);

        return Inertia::render('dashboard/settings/notification-recipient-rules/Index', [
            'translations' => [
                'title' => __('settings.notification_recipient_rules.page.title'),
                'coming_soon' => __('settings.notification_recipient_rules.page.coming_soon'),
            ],
        ]);
    }
}
