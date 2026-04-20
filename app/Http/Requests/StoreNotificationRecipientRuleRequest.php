<?php

namespace App\Http\Requests;

use App\Enums\RequestSeverity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreNotificationRecipientRuleRequest extends FormRequest
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
            'request_type_id' => ['nullable', Rule::exists('request_types', 'id')],
            'severity' => ['nullable', new Enum(RequestSeverity::class)],
            'recipient_user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('role', ['admin', 'engineer', 'viewer'])),
            ],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
