<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Database\Factories\RequestReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RequestReply extends Model
{
    /** @use HasFactory<RequestReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'inspection_request_id',
        'author_id',
        'body',
        'triggers_status',
    ];

    protected function casts(): array
    {
        return [
            'triggers_status' => RequestStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (RequestReply $reply): void {
            $reply->media()->delete();
        });
    }

    /**
     * @return BelongsTo<InspectionRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(InspectionRequest::class, 'inspection_request_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return MorphMany<RequestMedia, $this>
     */
    public function media(): MorphMany
    {
        return $this->morphMany(RequestMedia::class, 'mediable');
    }
}
