<?php

use App\Models\Workout;
use App\Models\Exercise;
use App\Models\Set;

use function Livewire\Volt\{mount, state, on};

state('workout', 'previousWorkouts');

mount(function (Workout $workout) {
    $workout->loadMissing(['user', 'exercises.movement', 'exercises.sets', 'template.exercises']);
    $this->previousExercises = $workout->previousExercises();
});

on([
    'exercises-added' => fn() => $this->workout->load(['exercises.movement', 'exercises.sets', 'template.exercises']),
    'note-saved' => fn() => $this->workout->load(['exercises.movement', 'exercises.sets', 'template.exercises']),
]);

$addSet = function (int $exerciseId) {
    $this->workout->exercises()->find($exerciseId)->sets()->create([
        'weight' => 0,
        'reps'   => 0,
    ]);

    $this->workout->load(['exercises.sets', 'template.exercises']);
};

$startWorkout = fn() => $this->workout->update(['started_at' => now()]);

$updateSet = function (int $setId, float $weight, int $reps) {
    $set = Set::find($setId);

    $set->update([
        'weight'       => $weight,
        'reps'         => $reps,
        'completed_at' => $set->completed_at ? null : now(),
    ]);

    $this->workout->load(['exercises.sets', 'template.exercises']);
};

$finishWorkout = function (?string $finishOption = null) {
    if ($finishOption === "with-template") {
        $this->workout->createTemplate();
    }

    $this->workout->update(['finished_at' => now()]);

    $this->redirectRoute('dashboard', navigate: true);
};

$reorder = function (array $ids) {
    foreach ($ids as $index => $id) {
        Exercise::where('id', $id)->update(['sequence' => $index]);
    }

    $this->workout->load(['exercises.movement', 'exercises.sets', 'template.exercises']);
};

$removeExercise = function (int $exerciseId) {
    $this->workout->exercises()->find($exerciseId)->delete();

    $this->workout->load(['exercises.movement', 'exercises.sets', 'template.exercises']);
};

$editNote = function (int $exerciseId, bool $sticky = false) {
    $this->dispatch('edit-note', exerciseId: $exerciseId, sticky: $sticky)->to('workout.edit-note');
};

$cancelWorkout = function () {
    $this->workout->delete();

    $this->redirectRoute('dashboard', navigate: true);
};

?>

<div class="max-w-2xl mx-auto p-6 space-y-8">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <div class="flex items-center justify-between top-0 z-10 bg-zinc-800 py-2">
        <flux:heading size="xl">{{ $workout->template->name ?? 'Workout' }}</flux:heading>

        <flux:dropdown align="end">
            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"/>

            <flux:menu>
                <flux:menu.item wire:click="cancelWorkout" icon="trash" variant="danger">Cancel Workout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
    <div
        x-data
        x-init="
            new Sortable($el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: (evt) => {
                    let ids = Array.from($el.children).map(el => el.getAttribute('data-id'));
                    $wire.reorder(ids);
                }
            });
        "
        class="space-y-6"
    >
        @forelse($workout->exercises as $exercise)
            <div data-id="{{ $exercise->id }}" wire:key="exercise-{{ $exercise->id }}">
                <x-exercise-component :exercise="$exercise" :previousExercises="$this->previousExercises"/>
            </div>
        @empty
            <div
                wire:key="no-exercises"
                class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 dark:border-white/10 rounded-2xl">
                <flux:text>No exercises added yet.</flux:text>
            </div>
        @endforelse
    </div>

    <div class="flex flex-col space-y-4">
        <flux:modal.trigger name="new-exercise">
            <flux:button tabindex="-1" class="w-full">Add New Exercise</flux:button>
        </flux:modal.trigger>
    </div>

    <div class="flex flex-col space-y-4">
        @if($workout->template)
            @if($workout->matchesTemplate())
                <flux:button wire:click="finishWorkout" variant="primary" color="emerald" class="w-full">Finish Workout</flux:button>
            @else
                <flux:modal.trigger name="finish-workout">
                    <flux:button variant="primary" color="emerald" class="w-full">Finish Workout</flux:button>
                </flux:modal.trigger>
            @endif
        @else
            <flux:modal.trigger name="finish-workout">
                <flux:button variant="primary" color="emerald" class="w-full">Finish Workout</flux:button>
            </flux:modal.trigger>
        @endif
    </div>

    <livewire:workout.add-exercises :$workout />

    <livewire:workout.finish-workout :$workout />

    <livewire:workout.edit-note :$workout />
</div>
