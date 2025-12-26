<section class="p-4 bg-zinc-50 dark:bg-white/5 rounded-2xl border border-zinc-200 dark:border-white/10 space-y-4">
    <flux:heading size="lg">{{ $exercise->movement->name }}</flux:heading>

    <div class="space-y-2">
        @foreach($exercise->sets as $set)
            <x-set-component :set="$set" :iteration="$loop->iteration" />
        @endforeach
    </div>

    <flux:button wire:click="addSet({{ $exercise->id }})" variant="ghost" icon="plus" size="sm" class="cursor-pointer" tabindex="-1">Add New Set</flux:button>
</section>
