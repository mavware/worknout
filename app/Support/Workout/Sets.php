<?php

namespace App\Support\Workout;

use Illuminate\Support\Collection;

class Sets extends Collection
{
    public function addSet(Set $set): self
    {
        $this->push($set);
        return $this;
    }
}
