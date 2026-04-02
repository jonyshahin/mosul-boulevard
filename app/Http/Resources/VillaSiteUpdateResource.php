<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillaSiteUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'villa_id' => $this->villa_id,
            'update_date' => $this->update_date?->toDateString(),
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'photos' => SiteUpdatePhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
