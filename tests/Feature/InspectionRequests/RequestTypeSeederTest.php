<?php

use App\Enums\RequestCategory;
use App\Models\RequestType;
use Database\Seeders\RequestTypeSeeder;

test('seeder creates the expected rows', function () {
    $this->seed(RequestTypeSeeder::class);

    $expected = [
        'Concrete Cover Defect', 'Rebar Placement Issue', 'Formwork Issue',
        'Finishing Quality', 'Dimensional Deviation',
        'PPE Violation', 'Fall Hazard', 'Housekeeping',
        'Fire Safety Violation', 'Electrical Hazard',
        'Material Approval Request', 'Material Rejection',
    ];

    $names = RequestType::pluck('name')->all();

    expect(count($names))->toBe(count($expected));

    foreach ($expected as $name) {
        expect($names)->toContain($name);
    }
});

test('seeder is idempotent', function () {
    $this->seed(RequestTypeSeeder::class);
    $firstCount = RequestType::count();
    $firstIds = RequestType::orderBy('id')->pluck('id')->all();

    $this->seed(RequestTypeSeeder::class);

    expect(RequestType::count())->toBe($firstCount)
        ->and(RequestType::orderBy('id')->pluck('id')->all())->toBe($firstIds);
});

test('seeder applies correct categories and colors', function () {
    $this->seed(RequestTypeSeeder::class);

    $concrete = RequestType::where('name', 'Concrete Cover Defect')->first();
    $ppe = RequestType::where('name', 'PPE Violation')->first();
    $material = RequestType::where('name', 'Material Approval Request')->first();

    expect($concrete->category)->toBe(RequestCategory::QaQc)
        ->and($concrete->color)->toBe('#1B4F72')
        ->and($ppe->category)->toBe(RequestCategory::Safety)
        ->and($ppe->color)->toBe('#C0392B')
        ->and($material->category)->toBe(RequestCategory::Materials)
        ->and($material->color)->toBe('#B8860B');
});

test('seeder assigns sort_order 10 stepping by 10', function () {
    $this->seed(RequestTypeSeeder::class);

    expect(RequestType::where('name', 'Concrete Cover Defect')->value('sort_order'))->toBe(10)
        ->and(RequestType::where('name', 'Rebar Placement Issue')->value('sort_order'))->toBe(20)
        ->and(RequestType::where('name', 'Material Rejection')->value('sort_order'))->toBe(120);
});
