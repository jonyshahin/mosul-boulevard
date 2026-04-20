<?php

namespace App\Models;

use App\Enums\RequestCategory;
use Database\Factories\RequestTypeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    /** @use HasFactory<RequestTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => RequestCategory::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<RequestType>  $query
     * @return Builder<RequestType>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<RequestType>  $query
     * @return Builder<RequestType>
     */
    public function scopeByCategory(Builder $query, RequestCategory $category): Builder
    {
        return $query->where('category', $category->value);
    }
}
