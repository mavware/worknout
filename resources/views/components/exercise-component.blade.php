<section class="p-4 bg-zinc-50 dark:bg-white/5 rounded-2xl border border-zinc-200 dark:border-white/10 space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ $exercise->movement->name }}</flux:heading>

        <div class="flex items-center gap-2">
            <flux:button variant="ghost" size="sm" icon="arrows-up-down" class="drag-handle cursor-grab" />

            <flux:dropdown align="end">
                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                <flux:menu>
                    <flux:menu.item wire:click="editNote({{ $exercise->id }})" icon="pencil-square">Add Note</flux:menu.item>
                    <flux:menu.item wire:click="editNote({{ $exercise->id }}, true)" icon="bookmark">Add Sticky Note</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="removeExercise({{ $exercise->id }})" icon="trash" variant="danger">Remove</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <div class="space-y-2">
        @if($exercise->templateExercise()?->note)
            <flux:text size="sm" class="italic">{{ $exercise->templateExercise()?->note }}</flux:text>
        @endif

        @if($exercise->note)
            <flux:text size="sm" class="italic">{{ $exercise->note }}</flux:text>
        @endif

        @foreach($exercise->sets as $set)
            <x-set-component :set="$set" :iteration="$loop->iteration" />
        @endforeach
    </div>

    <flux:button wire:click="addSet({{ $exercise->id }})" variant="ghost" icon="plus" size="sm" class="cursor-pointer" tabindex="-1">Add New Set</flux:button>
</section>
