<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Set extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'weight',
        'reps',
        'time',
        'position',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
