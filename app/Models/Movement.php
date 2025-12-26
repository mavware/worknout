<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function templates(): BelongsToMany
    {
        return $this->belongsToMany(Template::class)->withPivot('position')->withTimestamps();
    }
}
