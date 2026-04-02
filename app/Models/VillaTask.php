<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VillaTask extends Model
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
        'villa_id',
        'wbs_code',
        'task_name',
        'status_option_id',
        'planned_start',
        'planned_finish',
        'actual_start',
        'actual_finish',
        'completion_pct',
    ];

    protected function casts(): array
    {
        return [
            'planned_start' => 'date',
            'planned_finish' => 'date',
            'actual_start' => 'date',
            'actual_finish' => 'date',
            'completion_pct' => 'float',
        ];
    }

    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusOption::class, 'status_option_id');
    }
}
