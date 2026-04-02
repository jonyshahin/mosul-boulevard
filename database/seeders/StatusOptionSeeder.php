<?php

namespace Database\Seeders;

use App\Models\StatusOption;
use Illuminate\Database\Seeder;

class StatusOptionSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'unit' => [
                ['name' => 'Not Started', 'color_code' => '#6B7280'],
                ['name' => 'In Progress', 'color_code' => '#3B82F6'],
                ['name' => 'Delayed', 'color_code' => '#EF4444'],
                ['name' => 'Accelerated', 'color_code' => '#10B981'],
                ['name' => 'Finished', 'color_code' => '#22C55E'],
            ],
            'task' => [
                ['name' => 'Not Started', 'color_code' => '#6B7280'],
                ['name' => 'In Progress', 'color_code' => '#3B82F6'],
                ['name' => 'Delayed', 'color_code' => '#EF4444'],
                ['name' => 'Accelerated', 'color_code' => '#10B981'],
                ['name' => 'Finished', 'color_code' => '#22C55E'],
            ],
            'structural' => [
                ['name' => 'Raft Foundation'],
                ['name' => 'GF Slab'],
                ['name' => 'FF Slab'],
                ['name' => 'PH Slab'],
                ['name' => 'Finished Structure'],
            ],
            'finishing' => [
                ['name' => 'Not Started'],
                ['name' => 'Under Progress'],
                ['name' => 'Finished'],
            ],
            'facade' => [
                ['name' => 'Not Started'],
                ['name' => 'Under Progress'],
                ['name' => 'Finished'],
            ],
        ];

        foreach ($statuses as $category => $items) {
            foreach ($items as $sortOrder => $item) {
                StatusOption::create([
                    'category' => $category,
                    'name' => $item['name'],
                    'color_code' => $item['color_code'] ?? '#6B7280',
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }
}
