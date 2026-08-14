<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

trait ExplainsTrashedCodeConflict
{
    /**
     * Whether the submitted code is held by a soft-deleted record.
     *
     * Soft-deleted rows keep their code: the `unique` rule counts them and the
     * column carries a real unique index, so a deleted record reserves its code
     * indefinitely. Without this check the user is told the code is taken by
     * something they cannot find anywhere in the interface.
     *
     * @param  class-string<Model>  $model
     */
    protected function codeBelongsToTrashedRecord(string $model): bool
    {
        $code = $this->input('code');

        if (! is_string($code) || $code === '') {
            return false;
        }

        return $model::onlyTrashed()->where('code', $code)->exists();
    }
}
