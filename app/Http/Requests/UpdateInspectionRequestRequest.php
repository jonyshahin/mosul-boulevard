<?php

namespace App\Http\Requests;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateInspectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var InspectionRequest|null $request */
        $request = $this->route('inspection_request');

        if (! $request) {
            return false;
        }

        return ! in_array(
            $request->status,
            [RequestStatus::Resolved, RequestStatus::Verified, RequestStatus::Closed],
            true,
        );
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'assignee_id' => [
                'sometimes',
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('role', ['admin', 'engineer'])),
            ],
            'subject_type' => ['sometimes', 'required', 'in:villa,tower_unit'],
            'subject_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists($this->subjectTable(), 'id'),
            ],
            'request_type_id' => [
                'sometimes',
                'required',
                Rule::exists('request_types', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'location_detail' => ['sometimes', 'nullable', 'string', 'max:255'],
            'severity' => ['sometimes', 'required', new Enum(RequestSeverity::class)],
            'due_date' => ['sometimes', 'nullable', 'date', 'after:today'],
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
