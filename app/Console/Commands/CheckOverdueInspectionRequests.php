<?php

namespace App\Console\Commands;

use App\Enums\RequestStatus;
use App\Events\InspectionRequestOverdue;
use App\Models\InspectionRequest;
use Illuminate\Console\Command;

class CheckOverdueInspectionRequests extends Command
{
    protected $signature = 'inspection-requests:check-overdue';

    protected $description = 'Detect overdue inspection requests and dispatch InspectionRequestOverdue for each';

    public function handle(): int
    {
        $cutoff = now()->subHours(24);

        $query = InspectionRequest::query()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', [RequestStatus::Verified->value, RequestStatus::Closed->value])
            ->where(function ($q) use ($cutoff): void {
                $q->whereNull('overdue_notified_at')
                    ->orWhere('overdue_notified_at', '<', $cutoff);
            });

        $count = 0;

        $query->chunkById(100, function ($requests) use (&$count): void {
            foreach ($requests as $request) {
                $request->forceFill(['overdue_notified_at' => now()])->save();
                InspectionRequestOverdue::dispatch($request->fresh());
                $count++;
            }
        });

        $this->info("Dispatched overdue notifications for {$count} inspection requests.");

        return self::SUCCESS;
    }
}
