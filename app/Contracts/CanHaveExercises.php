<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveExercises
{
    public function exercises(): MorphMany;
}
