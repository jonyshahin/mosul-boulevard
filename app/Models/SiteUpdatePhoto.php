<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SiteUpdatePhoto extends Model
{
    protected $fillable = [
        'updateable_type',
        'updateable_id',
        'photo_path',
        'sort_order',
        'caption',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function updateable(): MorphTo
    {
        return $this->morphTo();
    }
}
