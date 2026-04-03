<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTowerUnitRequest extends FormRequest
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
            'code' => ['sometimes', 'required', 'string', 'unique:tower_units,code,'.($this->route('towerUnit')?->id ?? $this->route('tower_unit')?->id)],
            'tower_definition_id' => ['required', 'exists:tower_definitions,id'],
            'floor_definition_id' => ['nullable', 'exists:floor_definitions,id'],
            'is_sold' => ['boolean'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'sale_date' => ['nullable', 'date'],
            'current_stage_id' => ['nullable', 'exists:construction_stages,id'],
            'status_option_id' => ['nullable', 'exists:status_options,id'],
            'engineer_id' => ['nullable', 'exists:engineers,id'],
            'planned_start' => ['nullable', 'date'],
            'planned_finish' => ['nullable', 'date'],
            'actual_start' => ['nullable', 'date'],
            'actual_finish' => ['nullable', 'date'],
            'completion_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'acc_concrete_qty' => ['nullable', 'numeric', 'min:0'],
            'acc_steel_qty' => ['nullable', 'numeric', 'min:0'],
            'structural_status_id' => ['nullable', 'exists:status_options,id'],
            'finishing_status_id' => ['nullable', 'exists:status_options,id'],
            'facade_status_id' => ['nullable', 'exists:status_options,id'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
