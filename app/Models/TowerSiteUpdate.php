<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TowerSiteUpdate extends Model
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
        'tower_unit_id',
        'update_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'update_date' => 'date',
        ];
    }

    public function towerUnit(): BelongsTo
    {
        return $this->belongsTo(TowerUnit::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(SiteUpdatePhoto::class, 'updateable');
    }
}
