<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;

class WorkoutController extends Controller
{
    public function create(Request $request): Redirector|RedirectResponse
    {
        $template = Template::find($request->input('template_id'));

        $workout = auth()->user()->workouts()->create([
            'template_id' => $template?->id,
            'started_at' => now(),
        ]);

        foreach ($template?->exercises ?? [] as $exercise) {
            $newExercise = $workout->exercises()->create([
                'movement_id' => $exercise->movement_id,
                'position' => $exercise->position,
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

        return redirect(route('workout.edit', $workout));
    }
}
