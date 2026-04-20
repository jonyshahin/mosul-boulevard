<?php

namespace App\Notifications\Channels;

use App\Models\FcmNotificationLog;
use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Psr\Log\LoggerInterface;

class FcmChannel
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        if (! $notifiable instanceof User) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        $tokens = FcmToken::query()
            ->where('user_id', $notifiable->id)
            ->get();

        if ($tokens->isEmpty()) {
            return;
        }

        $relatable = $this->resolveNotifiable($notification);

        foreach ($tokens as $token) {
            $log = FcmNotificationLog::create([
                'user_id' => $notifiable->id,
                'notifiable_type' => $relatable['type'] ?? null,
                'notifiable_id' => $relatable['id'] ?? null,
                'token' => $token->token,
                'payload' => $payload,
                'status' => 'stub',
            ]);

            $this->logger->info('FCM stub dispatched', [
                'user_id' => $notifiable->id,
                'token_prefix' => substr($token->token, 0, 6).'...',
                'log_id' => $log->id,
            ]);
        }
    }

    /**
     * @return array{type: string|null, id: int|null}
     */
    private function resolveNotifiable(Notification $notification): array
    {
        foreach (['request', 'reply'] as $prop) {
            if (isset($notification->$prop)) {
                $target = $notification->$prop;

                return [
                    'type' => $target->getMorphClass(),
                    'id' => $target->getKey(),
                ];
            }
        }

        return ['type' => null, 'id' => null];
    }
}
