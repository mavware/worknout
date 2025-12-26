<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Exercise;
use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'movement_id' => Movement::factory(),
            'exercisable_id' => Template::factory(),
            'exercisable_type' => new Template()->getMorphClass(),
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}
