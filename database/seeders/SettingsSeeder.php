<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Academia'],
            ['key' => 'max_capacity_per_class', 'value' => '10'],
            ['key' => 'booking_advance_days', 'value' => '7'],
            ['key' => 'cancellation_hours', 'value' => '24'],
            ['key' => 'contact_email', 'value' => 'contato@academia.com'],
            ['key' => 'contact_phone', 'value' => '(11) 99999-9999'],
            ['key' => 'address', 'value' => 'São Paulo, SP'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
