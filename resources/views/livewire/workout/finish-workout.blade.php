<?php

use function Livewire\Volt\{state};

state(['workout']);

$finishWorkout = function (?string $finishOption = null) {
    if ($finishOption === "with-template") {
        $this->workout->createTemplate();
    }

    $this->workout->update(['finished_at' => now()]);

    $this->redirectRoute('dashboard', navigate: true);
};

?>

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
