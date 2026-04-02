<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillaResource extends JsonResource
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
            'code' => $this->code,
            'is_sold' => $this->is_sold,
            'customer_name' => $this->customer_name,
            'sale_date' => $this->sale_date?->toDateString(),
            'planned_start' => $this->planned_start?->toDateString(),
            'planned_finish' => $this->planned_finish?->toDateString(),
            'actual_start' => $this->actual_start?->toDateString(),
            'actual_finish' => $this->actual_finish?->toDateString(),
            'completion_pct' => $this->completion_pct,
            'acc_concrete_qty' => $this->acc_concrete_qty,
            'acc_steel_qty' => $this->acc_steel_qty,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'villa_type' => new VillaTypeResource($this->whenLoaded('villaType')),
            'current_stage' => new ConstructionStageResource($this->whenLoaded('currentStage')),
            'status' => new StatusOptionResource($this->whenLoaded('status')),
            'engineer' => new EngineerResource($this->whenLoaded('engineer')),
            'structural_status' => new StatusOptionResource($this->whenLoaded('structuralStatus')),
            'finishing_status' => new StatusOptionResource($this->whenLoaded('finishingStatus')),
            'facade_status' => new StatusOptionResource($this->whenLoaded('facadeStatus')),
            'villa_tasks' => VillaTaskResource::collection($this->whenLoaded('villaTasks')),
            'villa_site_updates' => VillaSiteUpdateResource::collection($this->whenLoaded('villaSiteUpdates')),
        ];
    }
}
