<?php

namespace App\Enums;

enum RequestSeverity: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Critical => 'Critical',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => '#10B981',
            self::Medium => '#F59E0B',
            self::High => '#EF4444',
            self::Critical => '#7F1D1D',
        };
    }

    public static function fromLabel(string $label): self
    {
        foreach (self::cases() as $case) {
            if (strcasecmp($case->label(), $label) === 0) {
                return $case;
            }
        }

        throw new \ValueError("No RequestSeverity matches label [{$label}]");
    }
}
