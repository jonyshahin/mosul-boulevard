<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        PropertyType::insert([
            ['name' => 'Villas', 'slug' => 'villas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Towers', 'slug' => 'towers', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
