<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'SettingSeeder']);

    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

test('index page loads with contact settings', function () {
    $response = $this->get(route('dashboard.settings.index'));

    $response->assertOk();
});

test('update saves settings', function () {
    $response = $this->put(route('dashboard.settings.update'), [
        'contact_phone' => '+964 750 999 8888',
        'contact_email' => 'updated@mosulboulevard.com',
        'contact_address' => 'New Address, Mosul',
        'contact_whatsapp' => '+964 750 999 8888',
        'contact_working_hours' => 'Sun-Thu 9am-5pm',
    ]);

    $response->assertRedirect(route('dashboard.settings.index'));

    expect(Setting::get('contact_phone'))->toBe('+964 750 999 8888')
        ->and(Setting::get('contact_email'))->toBe('updated@mosulboulevard.com')
        ->and(Setting::get('contact_address'))->toBe('New Address, Mosul')
        ->and(Setting::get('contact_whatsapp'))->toBe('+964 750 999 8888')
        ->and(Setting::get('contact_working_hours'))->toBe('Sun-Thu 9am-5pm');
});
