<?php

namespace App\Listeners;

use App\Events\RequestReplyCreated;
use App\Models\User;
use App\Notifications\InspectionRequestRepliedTo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\AsEventListener;

#[AsEventListener(event: RequestReplyCreated::class)]
class DispatchReplyNotifications implements ShouldQueue
{
    public string $connection = 'redis';

    public string $queue = 'notifications';

    public function handle(RequestReplyCreated $event): void
    {
        $reply = $event->reply;
        $authorId = $reply->author_id;
        $request = $reply->request;

        $targets = array_filter([
            $request->requester_id,
            $request->assignee_id,
        ], fn (int $uid) => $uid !== $authorId);

        foreach (array_unique($targets) as $userId) {
            if ($user = User::find($userId)) {
                $user->notify(new InspectionRequestRepliedTo($reply));
            }
        }
    }
}
