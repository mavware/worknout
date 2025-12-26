<?php

namespace App\Support\Workout;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Exercise implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $name,
        public Sets $sets,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'sets' => $this->sets->toArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        $sets = new Sets(array_map(
            fn ($set) => Set::fromArray($set),
            $data['sets'] ?? []
        ));

        return new self(
            name: $data['name'] ?? '',
            sets: $sets,
        );
    }
}
