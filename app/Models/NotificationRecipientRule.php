<?php

namespace App\Models;

use App\Enums\RequestSeverity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRecipientRule extends Model
{
    protected $fillable = [
        'request_type_id',
        'severity',
        'recipient_user_id',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'severity' => RequestSeverity::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<RequestType, $this>
     */
    public function requestType(): BelongsTo
    {
        return $this->belongsTo(RequestType::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * @param  Builder<NotificationRecipientRule>  $query
     * @return Builder<NotificationRecipientRule>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<NotificationRecipientRule>  $query
     * @return Builder<NotificationRecipientRule>
     */
    public function scopeMatching(Builder $query, RequestType $type, RequestSeverity $severity): Builder
    {
        return $query->active()
            ->where(function (Builder $q) use ($type): void {
                $q->whereNull('request_type_id')->orWhere('request_type_id', $type->id);
            })
            ->where(function (Builder $q) use ($severity): void {
                $q->whereNull('severity')->orWhere('severity', $severity->value);
            });
    }
}
