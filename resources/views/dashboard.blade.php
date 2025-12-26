<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex justify-center">
            <flux:button href="{{ route('workout.create') }}" variant="primary" color="emerald">
                START AN EMPTY WORKOUT
            </flux:button>
        </div>

        <div class="flex items-center justify-between px-1">
            <flux:heading size="xl" level="1">My Templates</flux:heading>

            <flux:modal.trigger name="create-template">
                <flux:button variant="ghost" icon="plus" inset="top bottom" class="cursor-pointer" />
            </flux:modal.trigger>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>

    <flux:modal name="create-template" class="md:w-96">
        <form action="{{ route('template.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <flux:heading size="lg">Create Template</flux:heading>
                <flux:text class="mt-2">Give your new workout template a name and description.</flux:text>
            </div>

            <div class="space-y-4">
                <flux:input label="Name" name="name" placeholder="e.g. Upper Body Power" required />

                <flux:textarea label="Description" name="description" placeholder="Briefly describe the focus of this template..." rows="3" />
            </div>

            <div class="flex">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:spacer />

                <flux:button type="submit" variant="primary">Create Template</flux:button>
            </div>
        </form>
    </flux:modal>
</x-layouts.app>
