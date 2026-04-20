<?php

namespace App\Http\Resources;

use App\Enums\RequestCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestTypeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $category = $this->category instanceof RequestCategory
            ? $this->category
            : RequestCategory::from($this->category);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => [
                'value' => $category->value,
                'label' => $category->label(),
            ],
            'color' => $this->color,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
