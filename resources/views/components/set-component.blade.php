<div x-data="{ weight: {{ $set->weight }}, reps: {{ $set->reps }} }">
    <form @submit.prevent="$wire.updateSet({{ $set->id }}, weight, reps)" class="flex items-center gap-4 bg-white dark:bg-white/5 p-3 rounded-xl border border-zinc-200 dark:border-white/10 shadow-sm">
        <flux:text weight="medium" class="flex-1">Set {{ $iteration }}</flux:text>

        <div class="flex items-center gap-2">
            <flux:text size="sm">Weight</flux:text>
            <flux:input x-model="weight" @focus="$el.select()" type="number" step="0.5" size="sm" class="w-20" />
        </div>

        <div class="flex items-center gap-2">
            <flux:text size="sm">Reps</flux:text>
            <flux:input x-model="reps" @focus="$el.select()" type="number" size="sm" class="w-16" />
        </div>

        <flux:button type="submit" :color="$set->completed_at ? 'emerald' : 'zinc'" variant="primary" icon="check" size="sm" class="cursor-pointer" />
    </form>
</div>
