<?php

use Illuminate\Support\Collection;
use function Livewire\Volt\{state, computed};

state(['exercises' => []]);

$data = computed(function () {
    return collect($this->exercises)->map(function ($exercise) {
        return $exercise->sets->max('weight') ?? 0;
    })->reverse()->values()->all();
});

?>

<div>
    @if(count($this->data) > 1)
        @php
            $min = min($this->data);
            $max = max($this->data);
            $range = $max - $min == 0 ? 1 : $max - $min;
            $width = 100;
            $height = 30;
            $padding = 2;
            $points = [];
            $count = count($this->data);
            foreach ($this->data as $index => $value) {
                $x = ($index / ($count - 1)) * $width;
                $y = $height - (($value - $min) / $range * ($height - 2 * $padding) + $padding);
                $points[] = "$x,$y";
            }
            $pointsString = implode(' ', $points);
        @endphp

        <svg width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" class="overflow-visible">
            <polyline
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                points="{{ $pointsString }}"
                class="text-emerald-500"
            />
        </svg>
    @endif
</div>
