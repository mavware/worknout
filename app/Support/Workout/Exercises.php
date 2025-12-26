<?php

namespace App\Support\Workout;

use Illuminate\Support\Collection;

class Exercises extends Collection
{
    public function addExercise(Exercise $exercise): self
    {
        $this->push($exercise);
        return $this;
    }
}
