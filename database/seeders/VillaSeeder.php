<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $villas = [];

        // Type A: A-001 through A-165
        for ($i = 1; $i <= 165; $i++) {
            $villas[] = [
                'code' => sprintf('A-%03d', $i),
                'villa_type_id' => 1,
                'is_sold' => false,
                'completion_pct' => 0,
                'acc_concrete_qty' => 0,
                'acc_steel_qty' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Type B: B-001 through B-076
        for ($i = 1; $i <= 76; $i++) {
            $villas[] = [
                'code' => sprintf('B-%03d', $i),
                'villa_type_id' => 2,
                'is_sold' => false,
                'completion_pct' => 0,
                'acc_concrete_qty' => 0,
                'acc_steel_qty' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($villas, 50) as $chunk) {
            DB::table('villas')->insert($chunk);
        }
    }
}
