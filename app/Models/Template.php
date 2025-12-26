<?php

namespace App\Models;

use App\Casts\RoutineCast;
use App\Trait\HasExercises;
use App\Contracts\CanHaveExercises;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model implements CanHaveExercises
{
    use HasExercises, HasFactory;

    protected $fillable = ['user_id', 'name', 'description'];

    protected $casts = [
        'routine' => RoutineCast::class,
    ];

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }
}
