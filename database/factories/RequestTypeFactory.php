<?php

namespace Database\Factories;

use App\Enums\RequestCategory;
use App\Models\RequestType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestType>
 */
class RequestTypeFactory extends Factory
{
    protected $model = RequestType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'category' => fake()->randomElement(RequestCategory::cases())->value,
            'color' => '#B8860B',
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function category(RequestCategory $category): static
    {
        return $this->state(fn () => ['category' => $category->value]);
    }
}
