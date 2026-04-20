<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InspectionRequestDetailResource extends InspectionRequestResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'description' => $this->description,
            'media' => RequestMediaResource::collection($this->whenLoaded('media')),
            'replies' => RequestReplyResource::collection($this->whenLoaded('replies')),
        ]);
    }
}
