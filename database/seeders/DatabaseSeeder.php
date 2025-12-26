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

        foreach($this->movements() as $movement){
            Movement::create(['name' => $movement]);
        }
    }

    private function movements(): array
    {
        return [
            'Ab Wheel',
            'Aerobics',
            'Arnold Press',
            'Around the World',
            'Back Extension',
            'Back Extension (Machine)',
            'Ball Slams',
            'Battle Ropes',
            'Bench Dip',
            'Bench Press (Barbell)',
            'Bench Press (Cable)',
            'Bench Press (Smith Machine)',
            'Bench Press - Close Grip (Barbell)',
            'Bench Press - Wide Grip (Barbell)',
            'Bent Over One Arm Row (Dumbbell)',
            'Bent Over Row (Band)',
            'Bent Over Row (Barbell)',
            'Bent Over Row (Dumbbell)',
            'Bent Over Row - Underhand (Barbell)',
            'Bicep Curl (Barbell)',
            'Bicep Curl (Cable)',
            'Bicep Curl (Dumbbell)',
            'Bicep Curl (Machine)',
            'Bicycle Crunch',
            'Box Jump',
            'Box Squat (Barbell)',
            'Bulgarian Split Squat',
            'Burpee',
        ];
    }
}
