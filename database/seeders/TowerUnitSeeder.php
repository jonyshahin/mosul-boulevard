<?php

namespace Database\Seeders;

use App\Models\FloorDefinition;
use App\Models\TowerDefinition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TowerUnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $towers = TowerDefinition::all();

        // Pre-load all floor definitions keyed by tower_id and floor_number
        $floors = FloorDefinition::all()->groupBy('tower_definition_id');

        $units = [];

        foreach ($towers as $tower) {
            $towerFloors = $floors->get($tower->id, collect())->keyBy('floor_number');

            for ($unit = 1; $unit <= 80; $unit++) {
                $floorNumber = (int) ceil($unit / 4);
                $floor = $towerFloors->get($floorNumber);

                $units[] = [
                    'code' => sprintf('%s-%02d', $tower->code_prefix, $unit),
                    'tower_definition_id' => $tower->id,
                    'floor_definition_id' => $floor?->id,
                    'is_sold' => false,
                    'completion_pct' => 0,
                    'acc_concrete_qty' => 0,
                    'acc_steel_qty' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($units, 50) as $chunk) {
            DB::table('tower_units')->insert($chunk);
        }
    }
}
