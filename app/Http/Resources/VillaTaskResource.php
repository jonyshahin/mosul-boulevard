<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillaTaskResource extends JsonResource
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
            'wbs_code' => $this->wbs_code,
            'task_name' => $this->task_name,
            'planned_start' => $this->planned_start?->toDateString(),
            'planned_finish' => $this->planned_finish?->toDateString(),
            'actual_start' => $this->actual_start?->toDateString(),
            'actual_finish' => $this->actual_finish?->toDateString(),
            'completion_pct' => $this->completion_pct,
            'created_at' => $this->created_at,
            'status' => new StatusOptionResource($this->whenLoaded('status')),
        ];
    }
}
