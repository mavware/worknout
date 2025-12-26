<?php

use App\Models\User;
use App\Models\Workout;
use App\Models\Movement;
use App\Models\Exercise;
use App\Models\Set;

use function Livewire\Volt\{mount, state};

state('workout');
state(['movements' => fn() => Movement::all()]);
state(['selectedMovements' => []]);

mount(function (Workout $workout) {
    $workout->loadMissing('exercises');
});

$addExercises = function () {
    $this->workout->exercises()->createMany(
        collect($this->selectedMovements)
            ->map(fn($id) => ['movement_id' => $id])
            ->toArray()
    );

    $this->selectedMovements = [];

    $this->js("Flux.modal('new-exercise').close()");
};

$addSet = function (int $exerciseId) {
    Exercise::find($exerciseId)->sets()->create([
        'weight' => 0,
        'reps'   => 0,
    ]);
};

$startWorkout = fn() => $this->workout->update(['started_at' => now()]);

$updateSet = function (int $setId, float $weight, int $reps) {
    Set::find($setId)->update([
        'weight'       => $weight,
        'reps'         => $reps,
        'completed_at' => now(),
    ]);
};

$finishWorkout = function (?string $finishOption = null) {
    if ($finishOption === "with-template") {
        /** @var User $user */
        $user = auth()->user();
        $template = $user->templates()->create([
            'name' => now()->format('M j, Y') . " Workout Template"
        ]);

        foreach ($this->workout->exercises as $exercise) {
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
    }

    $this->workout->update(['finished_at' => now()]);

    $this->redirectRoute('dashboard', navigate: true);
};

?>

<div class="max-w-2xl mx-auto p-6 space-y-8">
    <div class="flex items-center justify-between sticky top-0 z-10 bg-zinc-800">
        <flux:heading size="xl">{{ $workout->template->name ?? 'Workout' }}</flux:heading>
            <flux:modal.trigger name="finish-workout">
                <flux:button variant="primary" size="sm">Finish</flux:button>
            </flux:modal.trigger>
    </div>
    <div class="space-y-6">
        @forelse($workout->exercises as $exercise)
            <x-exercise-component :exercise="$exercise"/>
        @empty
            <div
                class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 dark:border-white/10 rounded-2xl">
                <flux:text>No exercises added yet.</flux:text>
            </div>
        @endforelse
    </div>

    <flux:modal.trigger name="new-exercise">
        <flux:button tabindex="-1">Add New Exercise</flux:button>
    </flux:modal.trigger>

    <flux:modal name="new-exercise" class="md:w-96">
        <form wire:submit="addExercises" class="space-y-6">
            <div>
                <flux:heading size="lg">Add Exercises</flux:heading>
                <flux:text class="mt-2">Select the movements you want to add to your workout.</flux:text>
            </div>

            <div class="space-y-2">
                @foreach($movements as $movement)
                    <flux:checkbox wire:model="selectedMovements" :value="$movement->id" :label="$movement->name"/>
                @endforeach
            </div>

            <div class="flex">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:spacer/>
                <flux:button type="submit" variant="primary">Add to Workout</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="finish-workout" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Finish Workout</flux:heading>
                <flux:text class="mt-2">Would you like to create a template from this workout?</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:spacer/>
                <flux:button wire:click="finishWorkout" variant="ghost">No</flux:button>
                <flux:button wire:click="finishWorkout('with-template')" variant="primary">Yes</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
