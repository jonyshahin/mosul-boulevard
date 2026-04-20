<?php

namespace App\Http\Requests;

use App\Enums\RequestSeverity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateNotificationRecipientRuleRequest extends FormRequest
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
            'request_type_id' => ['sometimes', 'nullable', Rule::exists('request_types', 'id')],
            'severity' => ['sometimes', 'nullable', new Enum(RequestSeverity::class)],
            'recipient_user_id' => [
                'sometimes',
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('role', ['admin', 'engineer', 'viewer'])),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
