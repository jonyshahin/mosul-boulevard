<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Image',
            self::Video => 'Video',
        };
    }

    public static function fromMimeType(string $mime): self
    {
        $prefix = strtolower(strtok($mime, '/'));

        return match ($prefix) {
            'image' => self::Image,
            'video' => self::Video,
            default => throw new \ValueError("Unsupported MIME type [{$mime}] for MediaType"),
        };
    }
}
