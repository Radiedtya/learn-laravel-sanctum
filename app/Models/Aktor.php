<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Guarded(['id'])]
#[Hidden(['pivot'])]
class Aktor extends Model
{
    /** @use HasFactory<\Database\Factories\AktorFactory> */
    use HasFactory;

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
