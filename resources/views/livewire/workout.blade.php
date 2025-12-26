<?php

use App\Models\Workout;
use App\Models\Movement;
use function Livewire\Volt\{mount, state};

state('workout');
state(['movements' => fn () => Movement::all()]);
state(['selectedMovements' => []]);

mount(function (Workout $workout) {
    $workout->loadMissing('exercises');
});

$addExercises = function () {
    $this->workout->exercises()->createMany(
        collect($this->selectedMovements)
            ->map(fn ($id) => ['movement_id' => $id])
            ->toArray()
    );

    $this->selectedMovements = [];

    $this->js("Flux.modal('new-exercise').close()");
};

$startWorkout = fn () => $this->workout->update(['started_at' => now()]);

?>

<div>
    <div>
        {{ $workout->template->name }}
    </div>
    @forelse($workout->exercises as $exercise)
        <x-exercise-component :sets="$exercise->sets"/>
    @empty
        {{-- Empty state --}}
    @endforelse

    <flux:modal.trigger name="new-exercise">
        <flux:button>Add New Exercise</flux:button>
    </flux:modal.trigger>

    <flux:modal name="new-exercise" class="md:w-96">
        <form wire:submit="addExercises" class="space-y-6">
            <div>
                <flux:heading size="lg">Add Exercises</flux:heading>
                <flux:text class="mt-2">Select the movements you want to add to your workout.</flux:text>
            </div>

            <div class="space-y-2">
                @foreach($movements as $movement)
                    <flux:checkbox wire:model="selectedMovements" :value="$movement->id" :label="$movement->name" />
                @endforeach
            </div>

            <div class="flex">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:spacer />
                <flux:button type="submit" variant="primary">Add to Workout</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
