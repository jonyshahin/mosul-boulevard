<?php

namespace App\Concerns;

trait NormalizesProgressFields
{
    /**
     * Columns that are NOT NULL with a database default of 0.
     *
     * @return array<int, string>
     */
    protected function progressFields(): array
    {
        return ['completion_pct', 'acc_concrete_qty', 'acc_steel_qty'];
    }

    /**
     * Turn explicitly submitted nulls into 0 for the progress columns.
     *
     * A database default only fires when the column is left out of the INSERT
     * altogether. An explicit null — which is what a blank number input sends —
     * reaches the NOT NULL constraint instead and the write fails.
     *
     * Fields that were not submitted at all are left untouched, so the database
     * default still applies on create and a partial update keeps the stored value.
     */
    protected function normalizeProgressFields(): void
    {
        $normalized = [];

        foreach ($this->progressFields() as $field) {
            if ($this->has($field) && $this->input($field) === null) {
                $normalized[$field] = 0;
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }
}
