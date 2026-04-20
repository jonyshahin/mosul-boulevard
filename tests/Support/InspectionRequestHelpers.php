<?php

namespace Tests\Support;

use App\Models\FloorDefinition;
use App\Models\RequestType;
use App\Models\TowerDefinition;
use App\Models\TowerUnit;
use App\Models\User;
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
        Artisan::call('db:seed', ['--class' => 'RequestTypeSeeder']);
    }

    public static function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public static function engineer(): User
    {
        return User::factory()->create(['role' => 'engineer']);
    }

    public static function viewer(): User
    {
        return User::factory()->create(['role' => 'viewer']);
    }

    public static function customer(): User
    {
        return User::factory()->create(['role' => 'customer']);
    }

    public static function activeRequestType(): RequestType
    {
        return RequestType::where('is_active', true)->first()
            ?? RequestType::factory()->create(['is_active' => true]);
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
