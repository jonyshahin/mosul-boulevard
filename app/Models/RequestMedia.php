<?php

namespace App\Models;

use App\Enums\MediaType;
use Database\Factories\RequestMediaFactory;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RequestMedia extends Model
{
    /** @use HasFactory<RequestMediaFactory> */
    use HasFactory;

    protected $table = 'request_media';

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'path',
        'disk',
        'mime_type',
        'media_type',
        'size_bytes',
        'original_name',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => MediaType::class,
            'size_bytes' => 'integer',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        $disk = app(FilesystemFactory::class)->disk($this->disk);

        try {
            return $disk->temporaryUrl($this->path, now()->addMinutes(30));
        } catch (\Throwable) {
            return $disk->url($this->path);
        }
    }
}
