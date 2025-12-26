<?php

use App\Models\Movement;
use function Livewire\Volt\{state, computed};

state(['workout']);
state(['search' => '']);
state(['selectedMovements' => []]);

$movements = computed(function () {
    return Movement::query()
        ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
        ->get();
});

$existingMovementIds = computed(function () {
    return $this->workout->exercises()->pluck('movement_id')->toArray();
});

$addExercises = function () {
    $this->workout->exercises()->createMany(
        collect($this->selectedMovements)
            ->map(fn($id) => ['movement_id' => $id])
            ->toArray()
    );

    $this->selectedMovements = [];

    $this->workout->load(['exercises.movement', 'exercises.sets', 'template.exercises']);

    $this->dispatch('exercises-added');

    $this->js("Flux.modal('new-exercise').close()");
};

?>

<flux:modal name="new-exercise" class="md:w-96">
    <form wire:submit="addExercises" class="space-y-6">
        <div>
            <flux:heading size="lg">Add Exercises</flux:heading>
            <flux:text class="mt-2">Select the movements you want to add to your workout.</flux:text>
        </div>

        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search movements..." />

        <div class="space-y-2 h-64 overflow-y-auto">
            @foreach($this->movements as $movement)
                @if(in_array($movement->id, $this->existingMovementIds))
                    <flux:checkbox checked disabled :label="$movement->name"/>
                @else
                    <flux:checkbox wire:model="selectedMovements" :value="$movement->id" :label="$movement->name"/>
                @endif
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
