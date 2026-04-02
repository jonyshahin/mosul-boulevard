<?php

namespace Database\Seeders;

use App\Models\ConstructionStage;
use Illuminate\Database\Seeder;

class ConstructionStageSeeder extends Seeder
{
    public function run(): void
    {
        $villaStages = [
            'Blinding',
            'Raft Foundation',
            'GF Columns',
            'GF Slab & Beams',
            'GF Staircase',
            'FF Columns',
            'FF Slab & Beams',
            'FF Staircase',
            'PH Columns',
            'PH Slab & Beams',
            'PH Staircase',
            'GF Masonry Works',
            'GF Lintels',
            'FF Masonry Works',
            'FF Lintels',
            'PH Masonry Works',
            'PH Lintels',
        ];

        foreach ($villaStages as $sortOrder => $name) {
            ConstructionStage::create([
                'property_type_id' => 1,
                'name' => $name,
                'sort_order' => $sortOrder,
            ]);
        }

        $towerStages = [
            'Excavation',
            'Backfilling',
            'Piles',
            'Raft Foundation',
            'B-Retaining Walls',
            'B-Columns & Staircases',
            'B-Slabs & Beams',
            'GF-Columns & Staircases',
            'GF-Slabs & Beams',
            '1st-Columns & Staircases',
            '1st-Slabs & Beams',
            '2nd-Columns & Staircases',
            '2nd-Slabs & Beams',
            '3rd-Columns & Staircases',
            '3-Slabs & Beams',
            '3-Columns & Staircases',
            '4-Slabs & Beams',
            '4-Columns & Staircases',
            '5-Slabs & Beams',
            '5-Columns & Staircases',
            '6-Slabs & Beams',
            '6-Columns & Staircases',
            '7-Slabs & Beams',
            '7-Columns & Staircases',
            '8-Slabs & Beams',
            '8-Columns & Staircases',
            '9-Slabs & Beams',
            '9-Columns & Staircases',
            '10-Slabs & Beams',
            '10-Columns & Staircases',
            '11-Slabs & Beams',
            '11-Columns & Staircases',
            '12-Slabs & Beams',
            '12-Columns & Staircases',
            '13-Slabs & Beams',
            '13-Columns & Staircases',
            '14-Slabs & Beams',
            '14-Columns & Staircases',
            '15-Slabs & Beams',
            '15-Columns & Staircases',
            '16-Slabs & Beams',
            '16-Columns & Staircases',
            '17-Slabs & Beams',
            '18-Columns & Staircases',
            '18-Slabs & Beams',
            '19-Columns & Staircases',
            '19-Slabs & Beams',
            '20-Columns & Staircases',
            '20-Slabs & Beams',
            'GF-Masonry Works',
            '1st-Masonry Works',
            '2nd-Masonry Works',
            '3-Masonry Works',
            '4-Masonry Works',
            '5-Masonry Works',
            '6-Masonry Works',
            '7-Masonry Works',
            '8-Masonry Works',
            '9-Masonry Works',
            '10-Masonry Works',
            '11-Masonry Works',
            '12-Masonry Works',
            '13-Masonry Works',
            '14-Masonry Works',
            '15-Masonry Works',
            '16-Masonry Works',
            '17-Masonry Works',
            '18-Masonry Works',
            '19-Masonry Works',
            '20-Masonry Works',
        ];

        foreach ($towerStages as $sortOrder => $name) {
            ConstructionStage::create([
                'property_type_id' => 2,
                'name' => $name,
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
