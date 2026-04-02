<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TowerDefinitionResource extends JsonResource
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
            'name' => $this->name,
            'code_prefix' => $this->code_prefix,
            'total_floors' => $this->total_floors,
            'units_per_floor' => $this->units_per_floor,
            'is_active' => $this->is_active,
        ];
    }
}
