<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VillaType extends Model
{
    protected $fillable = [
        'name',
        'code_prefix',
        'total_count',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_count' => 'integer',
        ];
    }

    public function villas(): HasMany
    {
        return $this->hasMany(Villa::class);
    }
}
