<?php

namespace App\Http\Requests;

use App\Enums\RequestCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreRequestTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:request_types,name'],
            'category' => ['required', new Enum(RequestCategory::class)],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
