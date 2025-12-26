<?php

namespace App\Support\Workout;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Set implements Arrayable, JsonSerializable
{
    public function __construct(
        public int $weight,
        public int $reps,
    ) {}

    public function toArray(): array
    {
        return [
            'weight' => $this->weight,
            'reps' => $this->reps,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            weight: $data['weight'],
            reps: $data['reps'],
        );
    }
}
