<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RequestMedia;
use App\Services\MediaUploadService;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RequestMediaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly MediaUploadService $uploadService,
        private readonly FilesystemFactory $filesystem,
    ) {}

    public function download(RequestMedia $media): RedirectResponse|JsonResponse
    {
        $this->authorize('view', $media);

        $disk = $this->filesystem->disk($media->disk);
        $ttl = (int) config('inspection_requests.signed_url_ttl_minutes');

        try {
            $url = $disk->temporaryUrl($media->path, now()->addMinutes($ttl));
        } catch (\Throwable) {
            $url = $disk->url($media->path);
        }

        return redirect()->away($url);
    }

    public function destroy(RequestMedia $media): JsonResponse
    {
        $this->authorize('delete', $media);

        $this->uploadService->delete($media);

        return response()->json(null, 204);
    }
}
