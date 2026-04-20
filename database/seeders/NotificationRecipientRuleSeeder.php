<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NotificationRecipientRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Intentionally empty by default — admins configure recipient rules
        // via the dashboard once production staff user IDs are known.
        //
        // Example (uncomment and fill in with a real project-manager user id):
        //
        // use App\Enums\RequestCategory;
        // use App\Enums\RequestSeverity;
        // use App\Models\NotificationRecipientRule;
        // use App\Models\RequestType;
        //
        // $pmId = 1; // project manager user id
        //
        // // Every critical-severity request pages the PM, regardless of type
        // NotificationRecipientRule::updateOrCreate(
        //     ['request_type_id' => null, 'severity' => RequestSeverity::Critical->value, 'recipient_user_id' => $pmId],
        //     ['is_active' => true, 'sort_order' => 10, 'notes' => 'All critical-severity requests'],
        // );
        //
        // // Every Safety-category request pages the PM at any severity
        // foreach (RequestType::where('category', RequestCategory::Safety->value)->get() as $type) {
        //     NotificationRecipientRule::updateOrCreate(
        //         ['request_type_id' => $type->id, 'severity' => null, 'recipient_user_id' => $pmId],
        //         ['is_active' => true, 'sort_order' => 20, 'notes' => 'All safety'],
        //     );
        // }
    }
}
