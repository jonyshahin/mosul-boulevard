<?php

namespace App\Http\Requests;

use App\Enums\RequestCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateRequestTypeRequest extends FormRequest
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
        $typeId = $this->route('request_type')?->id ?? $this->route('request_type');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('request_types', 'name')->ignore($typeId),
            ],
            'category' => ['sometimes', 'required', new Enum(RequestCategory::class)],
            'color' => ['sometimes', 'required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
