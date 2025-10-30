<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'capacity_per_slot', 'value' => '5'],
            ['key' => 'open_time', 'value' => '06:00'],
            ['key' => 'close_time', 'value' => '22:00'],
            ['key' => 'slot_minutes', 'value' => '60'],
        ];
        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }
    }
}
