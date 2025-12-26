<?php

namespace App\Support\Workout;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Routine implements Arrayable, JsonSerializable
{
    public function __construct(
        public ?Exercises $exercises = null,
    ) {
        $this->exercises = $exercises ?? new Exercises();
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->exercises as $exercise) {
            $data[$exercise->name] = $exercise->sets->toArray();
        }

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function addExercise(string $name, Sets $sets): self
    {
        $this->exercises->push(new Exercise($name, $sets));
        return $this;
    }

    public static function fromArray(array $data): self
    {
        $exercises = new Exercises();

        foreach ($data as $name => $sets) {
            $exercises->push(new Exercise(
                name: $name,
                sets: new Sets(array_map(
                    fn ($set) => Set::fromArray($set),
                    $sets
                ))
            ));
        }

        return new self($exercises);
    }
}
