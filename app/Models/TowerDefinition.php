<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TowerDefinition extends Model
{
    protected $fillable = [
        'name',
        'code_prefix',
        'total_floors',
        'units_per_floor',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_floors' => 'integer',
            'units_per_floor' => 'integer',
        ];
    }

    public function towerUnits(): HasMany
    {
        return $this->hasMany(TowerUnit::class);
    }

    public function floorDefinitions(): HasMany
    {
        return $this->hasMany(FloorDefinition::class);
    }
}
