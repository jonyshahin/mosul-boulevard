<?php

namespace App\Http\Requests;

use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TransitionInspectionRequestRequest extends FormRequest
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
        return [
            'target_status' => [
                'required',
                new Enum(RequestStatus::class),
                function (string $attribute, mixed $value, Closure $fail): void {
                    /** @var InspectionRequest|null $request */
                    $request = $this->route('inspection_request');

                    if (! $request || ! is_string($value)) {
                        return;
                    }

                    $current = $request->status instanceof RequestStatus
                        ? $request->status
                        : RequestStatus::from((string) $request->status);

                    $target = RequestStatus::tryFrom($value);

                    if ($target === null || ! $current->canTransitionTo($target)) {
                        $fail("Cannot transition from [{$current->value}] to [{$value}].");
                    }
                },
            ],
            'note' => ['nullable', 'string'],
        ];
    }
}
