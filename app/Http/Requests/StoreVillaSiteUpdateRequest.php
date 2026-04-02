<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVillaSiteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'update_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'photos' => ['array', 'nullable'],
            'photos.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
