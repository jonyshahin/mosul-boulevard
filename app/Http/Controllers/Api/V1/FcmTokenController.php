<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFcmTokenRequest;
use App\Http\Resources\FcmTokenResource;
use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(StoreFcmTokenRequest $request): JsonResponse
    {
        $data = $request->validated();

        $token = FcmToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => $request->user()->id,
                'device_id' => $data['device_id'] ?? null,
                'platform' => $data['platform'],
                'last_used_at' => now(),
            ],
        );

        return (new FcmTokenResource($token))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request, string $token): JsonResponse
    {
        $fcmToken = FcmToken::where('token', $token)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $fcmToken->delete();

        return response()->json(null, 204);
    }
}
