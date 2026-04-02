<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FloorDefinition extends Model
{
    protected $fillable = [
        'tower_definition_id',
        'name',
        'floor_number',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'floor_number' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function towerDefinition(): BelongsTo
    {
        return $this->belongsTo(TowerDefinition::class);
    }

    public function towerUnits(): HasMany
    {
        return $this->hasMany(TowerUnit::class);
    }
}
