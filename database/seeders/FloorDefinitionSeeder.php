<?php

namespace Database\Seeders;

use App\Models\FloorDefinition;
use App\Models\TowerDefinition;
use Illuminate\Database\Seeder;

class FloorDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $towers = TowerDefinition::all();

        foreach ($towers as $tower) {
            for ($floor = 1; $floor <= 20; $floor++) {
                FloorDefinition::create([
                    'tower_definition_id' => $tower->id,
                    'name' => $this->ordinal($floor).' Floor',
                    'floor_number' => $floor,
                    'sort_order' => $floor,
                ]);
            }
        }
    }

    private function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];

        $mod100 = $number % 100;

        return $number.($suffixes[($mod100 - 20) % 10] ?? $suffixes[$mod100] ?? $suffixes[0]);
    }
}
