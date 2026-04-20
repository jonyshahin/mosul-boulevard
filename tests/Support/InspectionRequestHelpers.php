<?php

namespace Tests\Support;

use App\Models\FloorDefinition;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\Villa;
use App\Models\VillaType;
use Illuminate\Support\Facades\Artisan;

class InspectionRequestHelpers
{
    public static function seedRequiredLookups(): void
    {
        Artisan::call('db:seed', ['--class' => 'PropertyTypeSeeder']);
        Artisan::call('db:seed', ['--class' => 'VillaTypeSeeder']);
        Artisan::call('db:seed', ['--class' => 'TowerDefinitionSeeder']);
        Artisan::call('db:seed', ['--class' => 'FloorDefinitionSeeder']);
    }

    public static function makeVilla(): Villa
    {
        return Villa::create([
            'code' => 'V-'.uniqid('', true),
            'villa_type_id' => VillaType::first()->id,
        ]);
    }

    public static function makeTowerUnit(): TowerUnit
    {
        return TowerUnit::create([
            'code' => 'TU-'.uniqid('', true),
            'tower_definition_id' => TowerDefinition::first()->id,
            'floor_definition_id' => FloorDefinition::first()->id,
        ]);
    }
}
