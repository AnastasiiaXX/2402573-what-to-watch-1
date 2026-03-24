<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\FilmsController;
use App\Http\Controllers\Api\GenresController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\CommentsController;

Route::name('user.')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UsersController::class, 'show']);
    Route::patch('/user', [UsersController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/comments/{film}', [CommentsController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentsController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentsController::class, 'destroy']);
    Route::get('/favourite', [FavoritesController::class, 'index']);
    Route::post('/films/{film}/favourite', [FavoritesController::class, 'store']);
    Route::delete('/films/{film}/favourite', [FavoritesController::class, 'destroy']);
});

Route::name('moderator.')->middleware(['auth:sanctum', 'role:isModerator'])->group(function () {
    Route::patch('/genres/{genre}', [GenresController::class, 'update']);
    Route::post('/promo/{film}', [FilmsController::class, 'storePromo']);
    Route::post('/films', [FilmsController::class, 'store']);
    Route::patch('/films/{film}', [FilmsController::class, 'update']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'store']);

Route::get('/films', [FilmsController::class, 'index']);
Route::get('/films/{film}', [FilmsController::class, 'show']);
Route::get('/films/{film}/similar', [FilmsController::class, 'indexSimilar']);
Route::get('/promo', [FilmsController::class, 'showPromo']);

Route::get('/genres/', [GenresController::class, 'index']);

Route::get('/comments/{film}', [CommentsController::class, 'index']);
