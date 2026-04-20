<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FcmTokenResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'token' => $this->maskToken($this->token),
            'device_id' => $this->device_id,
            'platform' => $this->platform,
            'last_used_at' => $this->last_used_at,
            'created_at' => $this->created_at,
        ];
    }

    private function maskToken(string $token): string
    {
        return strlen($token) <= 6
            ? str_repeat('*', strlen($token))
            : substr($token, 0, 6).'...';
    }
}
