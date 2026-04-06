<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function villas(): HasMany
    {
        return $this->hasMany(Villa::class);
    }

    public function towerUnits(): HasMany
    {
        return $this->hasMany(TowerUnit::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(isset($filters['search']) && $filters['search'] !== '', function (Builder $q) use ($filters) {
                $term = '%'.$filters['search'].'%';
                $q->where(function (Builder $sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when(isset($filters['is_active']), fn (Builder $q) => $q->where('is_active', $filters['is_active']));
    }
}
