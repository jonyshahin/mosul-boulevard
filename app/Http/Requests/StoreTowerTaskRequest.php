<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTowerTaskRequest extends FormRequest
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
            'wbs_code' => ['nullable', 'string', 'max:50'],
            'task_name' => ['required', 'string', 'max:255'],
            'status_option_id' => ['nullable', 'exists:status_options,id'],
            'planned_start' => ['nullable', 'date'],
            'planned_finish' => ['nullable', 'date'],
            'actual_start' => ['nullable', 'date'],
            'actual_finish' => ['nullable', 'date'],
            'completion_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
