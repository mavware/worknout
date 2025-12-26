<div>
    @forelse($sets as $set)
        <x-set-component />
        @empty
    @endforelse
</div>
