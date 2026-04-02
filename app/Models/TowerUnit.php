<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TowerUnit extends Model
{
    use LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'code',
        'tower_definition_id',
        'floor_definition_id',
        'is_sold',
        'customer_name',
        'sale_date',
        'current_stage_id',
        'status_option_id',
        'engineer_id',
        'planned_start',
        'planned_finish',
        'actual_start',
        'actual_finish',
        'completion_pct',
        'acc_concrete_qty',
        'acc_steel_qty',
        'structural_status_id',
        'finishing_status_id',
        'facade_status_id',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'is_sold' => 'boolean',
            'sale_date' => 'date',
            'planned_start' => 'date',
            'planned_finish' => 'date',
            'actual_start' => 'date',
            'actual_finish' => 'date',
            'completion_pct' => 'float',
            'acc_concrete_qty' => 'float',
            'acc_steel_qty' => 'float',
        ];
    }

    public function towerDefinition(): BelongsTo
    {
        return $this->belongsTo(TowerDefinition::class);
    }

    public function floorDefinition(): BelongsTo
    {
        return $this->belongsTo(FloorDefinition::class);
    }

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(ConstructionStage::class, 'current_stage_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusOption::class, 'status_option_id');
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Engineer::class);
    }

    public function structuralStatus(): BelongsTo
    {
        return $this->belongsTo(StatusOption::class, 'structural_status_id');
    }

    public function finishingStatus(): BelongsTo
    {
        return $this->belongsTo(StatusOption::class, 'finishing_status_id');
    }

    public function facadeStatus(): BelongsTo
    {
        return $this->belongsTo(StatusOption::class, 'facade_status_id');
    }

    public function towerTasks(): HasMany
    {
        return $this->hasMany(TowerTask::class);
    }

    public function towerSiteUpdates(): HasMany
    {
        return $this->hasMany(TowerSiteUpdate::class);
    }

    public function scopeSold(Builder $query): Builder
    {
        return $query->where('is_sold', true);
    }

    public function scopeUnsold(Builder $query): Builder
    {
        return $query->where('is_sold', false);
    }

    public function scopeForTower(Builder $query, int $towerId): Builder
    {
        return $query->where('tower_definition_id', $towerId);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(isset($filters['tower_definition_id']), fn (Builder $q) => $q->where('tower_definition_id', $filters['tower_definition_id']))
            ->when(isset($filters['floor_definition_id']), fn (Builder $q) => $q->where('floor_definition_id', $filters['floor_definition_id']))
            ->when(isset($filters['is_sold']), fn (Builder $q) => $q->where('is_sold', $filters['is_sold']))
            ->when(isset($filters['status_option_id']), fn (Builder $q) => $q->where('status_option_id', $filters['status_option_id']))
            ->when(isset($filters['engineer_id']), fn (Builder $q) => $q->where('engineer_id', $filters['engineer_id']))
            ->when(isset($filters['current_stage_id']), fn (Builder $q) => $q->where('current_stage_id', $filters['current_stage_id']))
            ->when(isset($filters['structural_status_id']), fn (Builder $q) => $q->where('structural_status_id', $filters['structural_status_id']))
            ->when(isset($filters['finishing_status_id']), fn (Builder $q) => $q->where('finishing_status_id', $filters['finishing_status_id']))
            ->when(isset($filters['facade_status_id']), fn (Builder $q) => $q->where('facade_status_id', $filters['facade_status_id']));
    }
}
