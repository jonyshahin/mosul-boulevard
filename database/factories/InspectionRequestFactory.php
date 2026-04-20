<?php

namespace Database\Factories;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Models\InspectionRequest;
use App\Models\RequestType;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<InspectionRequest>
 */
class InspectionRequestFactory extends Factory
{
    protected $model = InspectionRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'assignee_id' => User::factory(),
            'subject_type' => null,
            'subject_id' => null,
            'request_type_id' => RequestType::factory(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'location_detail' => fake()->optional()->sentence(3),
            'severity' => RequestSeverity::Medium->value,
            'status' => RequestStatus::Open->value,
            'due_date' => null,
            'resolved_at' => null,
            'verified_at' => null,
            'closed_at' => null,
            'verified_by' => null,
        ];
    }

    public function forSubject(Model $subject): static
    {
        return $this->state(fn () => [
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
        ]);
    }

    public function forVilla(Villa $villa): static
    {
        return $this->forSubject($villa);
    }

    public function forTowerUnit(TowerUnit $unit): static
    {
        return $this->forSubject($unit);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'due_date' => now()->subDays(3),
            'status' => RequestStatus::Open->value,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status' => RequestStatus::Resolved->value,
            'resolved_at' => now(),
        ]);
    }
}
