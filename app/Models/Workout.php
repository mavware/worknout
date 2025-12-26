<?php

namespace App\Models;

use App\Casts\RoutineCast;
use App\Trait\HasExercises;
use App\Contracts\CanHaveExercises;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workout extends Model implements CanHaveExercises
{
    use HasExercises, HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'name',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'routine' => RoutineCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
