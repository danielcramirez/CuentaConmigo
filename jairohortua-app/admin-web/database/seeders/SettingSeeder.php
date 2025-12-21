<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'notification_radius_km', 'value' => '20'],
            ['key' => 'notification_days_window', 'value' => '15'],
            ['key' => 'social_facebook_url', 'value' => 'https://facebook.com'],
            ['key' => 'social_instagram_url', 'value' => 'https://instagram.com'],
            ['key' => 'app_name', 'value' => 'Jairohortua'],
            ['key' => 'app_version', 'value' => '1.0.0'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
