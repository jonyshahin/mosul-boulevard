<?php

namespace Database\Seeders;

use App\Models\TowerDefinition;
use Illuminate\Database\Seeder;

class TowerDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            TowerDefinition::create([
                'name' => "Tower $i",
                'code_prefix' => "T$i",
                'total_floors' => 20,
                'units_per_floor' => 4,
            ]);
        }
    }
}
