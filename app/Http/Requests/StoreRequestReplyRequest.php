<?php

namespace App\Http\Requests;

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreRequestReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $maxKb = ((int) config('inspection_requests.max_file_size_mb')) * 1024;
        $mimes = array_merge(
            (array) config('inspection_requests.allowed_image_mimes'),
            (array) config('inspection_requests.allowed_video_mimes'),
        );

        return [
            'body' => ['required', 'string'],
            'triggers_status' => [
                'nullable',
                new Enum(RequestStatus::class),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    /** @var InspectionRequest|null $request */
                    $request = $this->route('inspection_request');

                    if (! $request) {
                        return;
                    }

                    $current = $request->status instanceof RequestStatus
                        ? $request->status
                        : RequestStatus::from((string) $request->status);

                    $target = RequestStatus::tryFrom((string) $value);

                    if ($target === null || ! $current->canTransitionTo($target)) {
                        $fail("Cannot transition from [{$current->value}] to [{$value}].");
                    }
                },
            ],
            'media' => ['nullable', 'array', 'max:10'],
            'media.*' => [
                'file',
                'max:'.$maxKb,
                'mimetypes:'.implode(',', $mimes),
            ],
        ];
    }
}
