<?php

namespace App\Http\Requests;

use App\Enums\RequestSeverity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreInspectionRequestRequest extends FormRequest
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
            'assignee_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query): void {
                    $query->whereIn('role', ['admin', 'engineer']);
                }),
            ],
            'subject_type' => ['required', 'in:villa,tower_unit'],
            'subject_id' => [
                'required',
                'integer',
                Rule::exists($this->subjectTable(), 'id'),
            ],
            'request_type_id' => [
                'required',
                Rule::exists('request_types', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location_detail' => ['nullable', 'string', 'max:255'],
            'severity' => ['required', new Enum(RequestSeverity::class)],
            'due_date' => ['nullable', 'date', 'after:today'],
            'media' => ['nullable', 'array', 'max:10'],
            'media.*' => [
                'file',
                'max:'.$maxKb,
                'mimetypes:'.implode(',', $mimes),
            ],
        ];
    }

    private function subjectTable(): string
    {
        return match ($this->input('subject_type')) {
            'tower_unit' => 'tower_units',
            default => 'villas',
        };
    }
}
