<?php

namespace Database\Seeders;

use App\Enums\RequestCategory;
use App\Models\RequestType;
use Illuminate\Database\Seeder;

class RequestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Concrete Cover Defect', 'category' => RequestCategory::QaQc, 'color' => '#1B4F72'],
            ['name' => 'Rebar Placement Issue', 'category' => RequestCategory::QaQc, 'color' => '#1B4F72'],
            ['name' => 'Formwork Issue', 'category' => RequestCategory::QaQc, 'color' => '#1B4F72'],
            ['name' => 'Finishing Quality', 'category' => RequestCategory::QaQc, 'color' => '#1B4F72'],
            ['name' => 'Dimensional Deviation', 'category' => RequestCategory::QaQc, 'color' => '#1B4F72'],
            ['name' => 'PPE Violation', 'category' => RequestCategory::Safety, 'color' => '#C0392B'],
            ['name' => 'Fall Hazard', 'category' => RequestCategory::Safety, 'color' => '#C0392B'],
            ['name' => 'Housekeeping', 'category' => RequestCategory::Safety, 'color' => '#C0392B'],
            ['name' => 'Fire Safety Violation', 'category' => RequestCategory::Safety, 'color' => '#C0392B'],
            ['name' => 'Electrical Hazard', 'category' => RequestCategory::Safety, 'color' => '#C0392B'],
            ['name' => 'Material Approval Request', 'category' => RequestCategory::Materials, 'color' => '#B8860B'],
            ['name' => 'Material Rejection', 'category' => RequestCategory::Materials, 'color' => '#B8860B'],
        ];

        $sortOrder = 10;
        foreach ($rows as $row) {
            RequestType::updateOrCreate(
                ['name' => $row['name']],
                [
                    'category' => $row['category']->value,
                    'color' => $row['color'],
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ],
            );
            $sortOrder += 10;
        }
    }
}
