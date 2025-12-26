<?php

use App\Models\User;
use App\Models\Workout;
use App\Models\Movement;
use App\Models\Exercise;
use App\Models\Set;
use App\Models\Template;

use function Livewire\Volt\{mount, state};

state('workout');
state(['movements' => fn() => Movement::all()]);
state(['selectedMovements' => []]);
state(['editingExercise' => null]);
state(['isSticky' => false]);
state(['note' => '']);

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

$reorder = function (array $ids) {
    foreach ($ids as $index => $id) {
        Exercise::where('id', $id)->update(['sequence' => $index]);
    }

    $this->workout->load('exercises');
};

$removeExercise = function (int $exerciseId) {
    Exercise::find($exerciseId)->delete();

    $this->workout->load('exercises');
};

$editNote = function (int $exerciseId, bool $sticky = false) {
    $this->editingExercise = Exercise::find($exerciseId);
    $this->isSticky = $sticky;
    $this->note = $this->editingExercise->note ?? '';

    $this->js("Flux.modal('edit-note').show()");
};

$saveNote = function () {
    $this->editingExercise->update(['note' => $this->note]);

    if ($this->isSticky && $this->workout->template_id) {
        $templateExercise = Exercise::where('exercisable_type', Template::class)
            ->where('exercisable_id', $this->workout->template_id)
            ->where('movement_id', $this->editingExercise->movement_id)
            ->first();

        if ($templateExercise) {
            $templateExercise->update(['note' => $this->note]);
        }
    }

    $this->editingExercise = null;
    $this->note = '';
    $this->isSticky = false;

    $this->workout->load('exercises');

    $this->js("Flux.modal('edit-note').close()");
};

?>

<div class="max-w-2xl mx-auto p-6 space-y-8">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <div class="flex items-center justify-between sticky top-0 z-10 bg-zinc-800">
        <flux:heading size="xl">{{ $workout->template->name ?? 'Workout' }}</flux:heading>
            <flux:modal.trigger name="finish-workout">
                <flux:button variant="primary" size="sm">Finish</flux:button>
            </flux:modal.trigger>
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
                <x-exercise-component :exercise="$exercise"/>
            </div>
        @empty
            <div
                wire:key="no-exercises"
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

    <flux:modal name="edit-note" class="md:w-96">
        <form wire:submit="saveNote" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $isSticky ? 'Add Sticky Note' : 'Add Note' }}</flux:heading>
                <flux:text class="mt-2">{{ $isSticky ? 'This note will be added to the template as well.' : 'Add a note to this exercise.' }}</flux:text>
            </div>

            <flux:textarea wire:model="note" placeholder="Enter note..." />

            <div class="flex">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:spacer/>
                <flux:button type="submit" variant="primary">Save Note</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
