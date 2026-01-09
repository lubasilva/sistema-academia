<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        // Only seed instructors and students if they don't exist yet
        if (User::where('role', 'instrutor')->count() === 0) {
            User::factory()->count(2)->create([
                'role' => 'instrutor',
            ]);
        }

        if (User::where('role', 'aluno')->count() === 0) {
            User::factory()->count(40)->create([
                'role' => 'aluno',
            ]);
        }
    }
}
