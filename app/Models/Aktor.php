<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['nama_aktor', 'gender', 'umur', 'foto'])]
class Aktor extends Model
{
    /** @use HasFactory<\Database\Factories\AktorFactory> */
    use HasFactory, HasApiTokens;
}
