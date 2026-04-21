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
            'media' => $this->mediaArray($request),
            'replies' => $this->repliesArray($request),
        ]);
    }

    /**
     * Unwrapped array form — avoids the `{data: [...]}` wrapping that
     * ResourceCollection adds, so Inertia props can be addressed directly.
     *
     * @return array<int, array<string, mixed>>
     */
    private function mediaArray(Request $request): array
    {
        if (! $this->relationLoaded('media')) {
            return [];
        }

        return collect($this->media)
            ->map(fn ($m) => (new RequestMediaResource($m))->resolve($request))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function repliesArray(Request $request): array
    {
        if (! $this->relationLoaded('replies')) {
            return [];
        }

        return collect($this->replies)
            ->map(fn ($r) => (new RequestReplyResource($r))->resolve($request))
            ->all();
    }
}
