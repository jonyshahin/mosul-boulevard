<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignInspectionRequestRequest extends FormRequest
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
            'assignee_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('role', ['admin', 'engineer'])),
            ],
        ];
    }
}
