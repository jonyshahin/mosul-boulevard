<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['label' => 'Villas Sales Status', 'property_type_id' => 1, 'sort_order' => 1],
            ['label' => 'Villas Structural Status', 'property_type_id' => 1, 'sort_order' => 2],
            ['label' => 'Villas Finishing Status', 'property_type_id' => 1, 'sort_order' => 3],
            ['label' => 'Villas Facade Status', 'property_type_id' => 1, 'sort_order' => 4],
            ['label' => 'Towers Sales Status', 'property_type_id' => 2, 'sort_order' => 5],
            ['label' => 'Towers Structural Status', 'property_type_id' => 2, 'sort_order' => 6],
            ['label' => 'Towers Finishing Status', 'property_type_id' => 2, 'sort_order' => 7],
            ['label' => 'Towers Facade Status', 'property_type_id' => 2, 'sort_order' => 8],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
