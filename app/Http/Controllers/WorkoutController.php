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
        $workout = auth()->user()->workouts()->create([
            'routine' => Template::find($request->input('template_id')) ?? [],
            'started_at' => now(),
        ]);

        return redirect(route('workout.edit', $workout));
    }
}
