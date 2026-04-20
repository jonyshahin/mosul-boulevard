<?php

namespace App\Http\Resources;

use App\Enums\MediaType;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestMediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mediaType = $this->media_type instanceof MediaType
            ? $this->media_type
            : MediaType::from($this->media_type);

        return [
            'id' => $this->id,
            'media_type' => [
                'value' => $mediaType->value,
                'label' => $mediaType->label(),
            ],
            'mime_type' => $this->mime_type,
            'size_bytes' => (int) $this->size_bytes,
            'original_name' => $this->original_name,
            'url' => $this->resolveUrl(),
            'uploader' => new UserResource($this->whenLoaded('uploader')),
            'created_at' => $this->created_at,
        ];
    }

    private function resolveUrl(): string
    {
        $disk = app(FilesystemFactory::class)->disk($this->disk);
        $ttl = (int) config('inspection_requests.signed_url_ttl_minutes');

        try {
            return $disk->temporaryUrl($this->path, now()->addMinutes($ttl));
        } catch (\Throwable) {
            return $disk->url($this->path);
        }
    }
}
