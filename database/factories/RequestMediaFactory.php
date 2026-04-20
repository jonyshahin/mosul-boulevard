<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Models\RequestMedia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestMedia>
 */
class RequestMediaFactory extends Factory
{
    protected $model = RequestMedia::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mediable_type' => null,
            'mediable_id' => null,
            'path' => 'requests/'.fake()->uuid().'.jpg',
            'disk' => 'r2',
            'mime_type' => 'image/jpeg',
            'media_type' => MediaType::Image->value,
            'size_bytes' => fake()->numberBetween(10_000, 5_000_000),
            'original_name' => fake()->word().'.jpg',
            'uploaded_by' => User::factory(),
        ];
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'path' => 'requests/'.fake()->uuid().'.mp4',
            'mime_type' => 'video/mp4',
            'media_type' => MediaType::Video->value,
            'original_name' => fake()->word().'.mp4',
            'size_bytes' => fake()->numberBetween(1_000_000, 50_000_000),
        ]);
    }
}
