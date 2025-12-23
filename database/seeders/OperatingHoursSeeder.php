<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperatingHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Horários de funcionamento padrão
            'operating_hours' => json_encode([
                'monday' => ['start' => '06:00', 'end' => '22:00', 'enabled' => true],
                'tuesday' => ['start' => '06:00', 'end' => '22:00', 'enabled' => true],
                'wednesday' => ['start' => '06:00', 'end' => '22:00', 'enabled' => true],
                'thursday' => ['start' => '06:00', 'end' => '22:00', 'enabled' => true],
                'friday' => ['start' => '06:00', 'end' => '22:00', 'enabled' => true],
                'saturday' => ['start' => '08:00', 'end' => '18:00', 'enabled' => true],
                'sunday' => ['start' => '08:00', 'end' => '14:00', 'enabled' => false],
            ]),
            
            // Intervalo de tempo entre slots (em minutos)
            'slot_duration' => '60',
            
            // Capacidade padrão por horário
            'default_capacity' => '20',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
