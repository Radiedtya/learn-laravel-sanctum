<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Guarded(['id'])]
class Film extends Model
{
    /** @use HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory;

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function aktors(): BelongsToMany
    {
        return $this->belongsToMany(Aktor::class);
    }
}
