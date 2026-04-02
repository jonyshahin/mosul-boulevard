<?php

namespace Database\Seeders;

use App\Models\Engineer;
use Illuminate\Database\Seeder;

class EngineerSeeder extends Seeder
{
    public function run(): void
    {
        Engineer::insert([
            ['name' => 'Hamza Al-Qaisy', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mustafa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
