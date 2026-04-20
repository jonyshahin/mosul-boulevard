<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Verified = 'verified';
    case Closed = 'closed';
    case Reopened = 'reopened';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::Resolved => 'Resolved',
            self::Verified => 'Verified',
            self::Closed => 'Closed',
            self::Reopened => 'Reopened',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => '#3B82F6',
            self::InProgress => '#F59E0B',
            self::Resolved => '#10B981',
            self::Verified => '#059669',
            self::Closed => '#6B7280',
            self::Reopened => '#EF4444',
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::Closed;
    }

    public function canTransitionTo(self $next): bool
    {
        $allowed = self::validTransitions()[$this->value] ?? [];

        return in_array($next->value, $allowed, true);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function validTransitions(): array
    {
        return [
            self::Open->value => [self::InProgress->value, self::Resolved->value, self::Closed->value],
            self::InProgress->value => [self::Resolved->value, self::Open->value],
            self::Resolved->value => [self::Verified->value, self::Reopened->value],
            self::Verified->value => [self::Closed->value],
            self::Closed->value => [self::Reopened->value],
            self::Reopened->value => [self::InProgress->value, self::Resolved->value],
        ];
    }
}
