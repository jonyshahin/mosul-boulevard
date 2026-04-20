<?php

namespace App\Models;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use Database\Factories\InspectionRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionRequest extends Model
{
    /** @use HasFactory<InspectionRequestFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requester_id',
        'assignee_id',
        'subject_type',
        'subject_id',
        'request_type_id',
        'title',
        'description',
        'location_detail',
        'severity',
        'status',
        'due_date',
        'resolved_at',
        'verified_at',
        'closed_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'severity' => RequestSeverity::class,
            'status' => RequestStatus::class,
            'due_date' => 'datetime',
            'resolved_at' => 'datetime',
            'verified_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (InspectionRequest $request): void {
            if ($request->isForceDeleting()) {
                $request->replies->each->delete();
                $request->media()->delete();
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * @return BelongsTo<RequestType, $this>
     */
    public function requestType(): BelongsTo
    {
        return $this->belongsTo(RequestType::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<RequestReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(RequestReply::class)->orderBy('created_at', 'asc');
    }

    /**
     * @return MorphMany<RequestMedia, $this>
     */
    public function media(): MorphMany
    {
        return $this->morphMany(RequestMedia::class, 'mediable');
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     * @return Builder<InspectionRequest>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::Open->value);
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     * @return Builder<InspectionRequest>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [
                RequestStatus::Verified->value,
                RequestStatus::Closed->value,
            ]);
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     * @return Builder<InspectionRequest>
     */
    public function scopeForAssignee(Builder $query, int $userId): Builder
    {
        return $query->where('assignee_id', $userId);
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     * @return Builder<InspectionRequest>
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     * @return Builder<InspectionRequest>
     */
    public function scopeBySeverity(Builder $query, RequestSeverity $severity): Builder
    {
        return $query->where('severity', $severity->value);
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     * @return Builder<InspectionRequest>
     */
    public function scopeByStatus(Builder $query, RequestStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }
}
