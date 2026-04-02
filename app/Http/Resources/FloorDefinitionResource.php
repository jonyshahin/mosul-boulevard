<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FloorDefinitionResource extends JsonResource
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
            'tower_definition_id' => $this->tower_definition_id,
            'name' => $this->name,
            'floor_number' => $this->floor_number,
            'sort_order' => $this->sort_order,
        ];
    }
}
