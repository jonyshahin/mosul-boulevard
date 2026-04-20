<?php

namespace App\Services;

use App\Enums\MediaType;
use App\Models\InspectionRequest;
use App\Models\RequestMedia;
use App\Models\RequestReply;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaUploadService
{
    public function __construct(
        private readonly FilesystemFactory $filesystem,
    ) {}

    public function store(UploadedFile $file, Model $mediable, User $uploader): RequestMedia
    {
        $disk = 'r2';
        $directory = $this->directoryFor($mediable);
        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $filename = Str::uuid()->toString().($extension !== '' ? '.'.$extension : '');
        $path = $directory.'/'.$filename;

        $this->filesystem->disk($disk)->putFileAs(
            $directory,
            $file,
            $filename,
        );

        $mime = $file->getMimeType() ?? 'application/octet-stream';

        return RequestMedia::create([
            'mediable_type' => $mediable->getMorphClass(),
            'mediable_id' => $mediable->getKey(),
            'path' => $path,
            'disk' => $disk,
            'mime_type' => $mime,
            'media_type' => MediaType::fromMimeType($mime)->value,
            'size_bytes' => $file->getSize() ?: 0,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => $uploader->id,
        ]);
    }

    public function delete(RequestMedia $media): void
    {
        $this->filesystem->disk($media->disk)->delete($media->path);
        $media->delete();
    }

    private function directoryFor(Model $mediable): string
    {
        return match (true) {
            $mediable instanceof InspectionRequest => 'inspection-requests/'.$mediable->getKey(),
            $mediable instanceof RequestReply => 'request-replies/'.$mediable->getKey(),
            default => 'request-media/'.str_replace('\\', '-', $mediable->getMorphClass()).'/'.$mediable->getKey(),
        };
    }
}
