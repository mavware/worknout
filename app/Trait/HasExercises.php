<?php

namespace App\Trait;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @require-implements CanHaveExercises
 * @require-extends Model
 */
trait HasExercises
{
    public function exercises(): MorphMany
    {
        return $this->morphMany(Exercise::class, 'exercisable')
            ->orderBy('sequence')
            ->orderBy('created_at');
    }
}
