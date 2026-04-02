<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PropertyTypeSeeder::class,
            VillaTypeSeeder::class,
            TowerDefinitionSeeder::class,
            EngineerSeeder::class,
            FloorDefinitionSeeder::class,
            StatusOptionSeeder::class,
            ConstructionStageSeeder::class,
            VillaSeeder::class,
            TowerUnitSeeder::class,
            MenuItemSeeder::class,
        ]);
    }
}
