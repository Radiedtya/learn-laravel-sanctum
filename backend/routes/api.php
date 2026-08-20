<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API for admin
use App\Http\Controllers\Api\AktorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\GenreController;

// Public API
use App\Http\Controllers\Api\PublicController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('genres', GenreController::class);
    Route::apiResource('aktors', AktorController::class);
    Route::apiResource('films', FilmController::class);

});

Route::prefix('public')->group(function () {

    Route::get('/films', [PublicController::class, 'films']);
    Route::get('/films/{id}', [PublicController::class, 'detailFilm']);

    Route::get('/genres', [PublicController::class, 'genres']);
    Route::get('/genres/{id}/films', [PublicController::class, 'filmByGenre']);

    Route::get('/actors', [PublicController::class, 'actors']);
    Route::get('/actors/{id}/films', [PublicController::class, 'filmByActor']);

    Route::get('/search', [PublicController::class, 'search']);

});