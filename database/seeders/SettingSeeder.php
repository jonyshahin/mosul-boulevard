<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $contactSettings = [
            'contact_phone' => '+964 770 000 0000',
            'contact_email' => 'info@mosulboulevard.com',
            'contact_address' => 'Mosul, Nineveh, Iraq',
            'contact_whatsapp' => '+964 770 000 0000',
            'contact_working_hours' => 'Sunday - Thursday: 9:00 AM - 5:00 PM',
        ];

        foreach ($contactSettings as $key => $value) {
            Setting::set($key, $value, 'contact');
        }
    }
}
