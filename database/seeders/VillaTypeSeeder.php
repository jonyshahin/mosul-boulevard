<?php

namespace Database\Seeders;

use App\Models\VillaType;
use Illuminate\Database\Seeder;

class VillaTypeSeeder extends Seeder
{
    public function run(): void
    {
        VillaType::insert([
            ['name' => 'Type A', 'code_prefix' => 'A', 'total_count' => 165, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Type B', 'code_prefix' => 'B', 'total_count' => 76, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
