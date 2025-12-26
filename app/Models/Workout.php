<?php

namespace App\Models;

use App\Casts\RoutineCast;
use App\Trait\HasExercises;
use App\Contracts\CanHaveExercises;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workout extends Model implements CanHaveExercises
{
    use HasExercises, HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'name',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'routine' => RoutineCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function createTemplate(): Template
    {
        $template = $this->user->templates()->create([
            'name' => now()->format('M j, Y') . " Workout Template"
        ]);

        foreach ($this->exercises as $exercise) {
            $newExercise = $template->exercises()->create([
                'movement_id' => $exercise->movement_id,
                'position'    => $exercise->position,
            ]);

            foreach ($exercise->sets as $set) {
                $newExercise->sets()->create([
                    'weight'   => $set->weight,
                    'reps'     => $set->reps,
                    'time'     => $set->time,
                    'position' => $set->position,
                ]);
            }
        }

        return $template;
    }
}
