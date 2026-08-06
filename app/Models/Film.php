<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['judul_film', 'slug', 'durasi', 'deskripsi', 'rating', 'tahun_rilis', 'poster', 'genre_id', 'sutradara'])]
class Film extends Model
{
    /** @use HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory, HasApiTokens;

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }
}
