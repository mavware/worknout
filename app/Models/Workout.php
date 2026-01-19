<?php

namespace App\Models;

use App\Casts\RoutineCast;
use App\Trait\HasExercises;
use Illuminate\Support\Collection;
use App\Contracts\CanHaveExercises;
use Illuminate\Database\Eloquent\Builder;
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

        $this->updateTemplate($template);

        return $template;
    }

    public function updateTemplate(?Template $template = null): void
    {
        $template ??= $this->template;

        if (!$template) {
            return;
        }

        $template->exercises()->delete();

        foreach ($this->exercises as $exercise) {
            $newExercise = $template->exercises()->create([
                'movement_id' => $exercise->movement_id,
                'position'    => $exercise->position,
                'sequence'    => $exercise->sequence,
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
    }

    public function matchesTemplate(): bool
    {
        if (!$this->template) {
            return false;
        }

        $workoutExercises = $this->exercises->values();
        $templateExercises = $this->template->exercises->values();

        if ($workoutExercises->count() !== $templateExercises->count()) {
            return false;
        }

        foreach ($workoutExercises as $index => $workoutExercise) {
            $templateExercise = $templateExercises[$index];

            if ($workoutExercise->movement_id !== $templateExercise->movement_id) {
                return false;
            }

            $workoutSets = $workoutExercise->sets->values();
            $templateSets = $templateExercise->sets->values();

            if ($workoutSets->count() !== $templateSets->count()) {
                return false;
            }

            foreach ($workoutSets as $setIndex => $workoutSet) {
                $templateSet = $templateSets[$setIndex];

                if ($workoutSet->weight != $templateSet->weight || $workoutSet->reps != $templateSet->reps) {
                    return false;
                }
            }
        }

        return true;
    }

    public function scopeWhereBefore(Builder $query, Workout $workout): Builder
    {
        return $query->where('id', '<', $workout->id);
    }

    public function previousExercises(): Collection
    {
        return $this->user
            ->workouts()
            ->with('exercises.sets')
            ->where('template_id', $this->template_id ?? '')
            ->whereBefore($this)
            ->latest()
            ->take(5)
            ->get()
            ->pluck('exercises')
            ->flatten();
    }
}
