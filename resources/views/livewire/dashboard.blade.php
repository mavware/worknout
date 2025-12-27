<?php

use App\Models\User;

use function Livewire\Volt\{mount, state};

state(['templates', 'name' => '', 'description' => '']);

mount(function () {
    /** @var User $user */
    $user = auth()->user();

    $this->templates = $user->templates;
    });

$createTemplate = function () {
    $validated = $this->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    auth()->user()->templates()->create($validated);

    $this->templates = auth()->user()->templates;

    $this->reset(['name', 'description']);

    $this->js("Flux.modal('create-template').close()");
};

?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex justify-center">
        <flux:button href="{{ route('workout.create') }}" wire:navigate variant="primary" color="emerald">
            START AN EMPTY WORKOUT
        </flux:button>
    </div>

    <div class="flex items-center justify-between px-1">
        <flux:heading size="xl" level="1">My Templates</flux:heading>

        <flux:modal.trigger name="create-template">
            <flux:button variant="ghost" icon="plus" inset="top bottom" class="cursor-pointer"/>
        </flux:modal.trigger>
    </div>

    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        @forelse($this->templates as $template)
            <a href="{{ route('workout.create', ['template_id' => $template->id]) }}" wire:navigate class="relative flex aspect-video flex-col items-start gap-2 rounded-xl border border-neutral-200 p-4 transition-colors hover:border-neutral-300 group dark:border-neutral-700 dark:hover:border-neutral-600">
                <flux:heading size="lg" class="group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $template->name }}</flux:heading>

                <flux:text class="line-clamp-3">
                    {{ $template->description }}
                    @foreach($template->exercises->take(5) as $exercise)
                        <div class="text-sm font-medium text-gray-400 dark:text-gray-300 px-3">
                            {{ $exercise->sets->count() }} X {{ $exercise->movement->name }}
                        </div>
                            @endforeach
                </flux:text>
            </a>
        @empty
        @endforelse

        @for($x = $this->templates->count() % 3; $x <= 2; $x++)
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>
            </div>
        @endfor

    </div>
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>
    </div>

    <flux:modal name="create-template" class="md:w-96">
        <form wire:submit="createTemplate" class="space-y-6">
            <div>
                <flux:heading size="lg">Create Template</flux:heading>
                <flux:text class="mt-2">Give your new workout template a name and description.</flux:text>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="name" label="Name" placeholder="e.g. Upper Body Power" required/>

                <flux:textarea wire:model="description" label="Description"
                               placeholder="Briefly describe the focus of this template..." rows="3"/>
            </div>

            <div class="flex">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:spacer/>

                <flux:button type="submit" variant="primary">Create Template</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
