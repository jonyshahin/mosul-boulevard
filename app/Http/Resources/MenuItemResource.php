<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
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
            'label' => $this->label,
            'image_path' => $this->image_path,
            'route_name' => $this->route_name,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'property_type' => new PropertyTypeResource($this->whenLoaded('propertyType')),
        ];
    }
}
