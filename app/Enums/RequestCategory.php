<?php

namespace App\Enums;

enum RequestCategory: string
{
    case QaQc = 'qaqc';
    case Safety = 'safety';
    case Materials = 'materials';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::QaQc => 'QA/QC',
            self::Safety => 'Safety',
            self::Materials => 'Materials',
            self::Other => 'Other',
        };
    }
}
