<?php

namespace Database\Factories;

use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestReply>
 */
class RequestReplyFactory extends Factory
{
    protected $model = RequestReply::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inspection_request_id' => InspectionRequest::factory(),
            'author_id' => User::factory(),
            'body' => fake()->paragraph(),
            'triggers_status' => null,
        ];
    }
}
