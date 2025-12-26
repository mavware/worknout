<?php

use App\Models\Template;
use App\Models\Exercise;
use function Livewire\Volt\{state, on};

state(['workout']);
state(['editingExercise' => null]);
state(['isSticky' => false]);
state(['note' => '']);

on(['edit-note' => function (int $exerciseId, bool $sticky = false) {
    $this->editingExercise = Exercise::find($exerciseId);
    $this->isSticky = $sticky;
    $this->note = $this->editingExercise->note ?? '';

    $this->js("Flux.modal('edit-note').show()");
}]);

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

    $this->dispatch('note-saved');

    $this->js("Flux.modal('edit-note').close()");
};

?>

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
