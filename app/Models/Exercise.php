<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercisable_id',
        'exercisable_type',
        'movement_id',
        'position',
        'sequence',
    ];

    public function exercisable(): MorphTo
    {
        return $this->morphTo();
    }

    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
    }
}
