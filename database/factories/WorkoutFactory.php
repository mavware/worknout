<?php

namespace Database\Factories;

use App\Models\Template;
use App\Models\User;
use App\Support\Workout\Routine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'template_id' => Template::factory(),
            'routine' => new Routine(),
            'started_at' => now(),
            'finished_at' => now()->addHour(),
        ];
    }
}
