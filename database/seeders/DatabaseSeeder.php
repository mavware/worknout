<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Movement;
use Illuminate\Database\Seeder;
use App\Support\Workout\Exercise;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'mlgreer1430@gmail.com'],
            [
                'name' => 'Michael Greer',
                'password' => 'Bed4zzle$',
                'email_verified_at' => now(),
            ]
        );

        $movements = json_decode(file_get_contents(database_path('data/movements.json')), true);

        foreach($movements as $movement){
            Movement::create(['name' => $movement]);
        }
    }
}
