<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        \App\Models\User::factory()->count(2)->create([
            'role' => 'instrutor',
        ]);

        \App\Models\User::factory()->count(40)->create([
            'role' => 'aluno',
        ]);
    }
}
