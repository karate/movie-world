<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReactionController;
use Illuminate\Support\Facades\Route;

Route::get('/phpinfo', function () {
    phpinfo();
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User's movies management
    Route::get('/profile/movies', [ProfileController::class, 'movies'])->name('profile.movies');
    Route::patch('/profile/movies/{movie}', [ProfileController::class, 'updateMovie'])->name('profile.movie.update');
});

Route::get('/', [MovieController::class,'index'])->name('home');
Route::resource('movies', MovieController::class)->except(['index','show'])->middleware('auth');
Route::resource('movies', MovieController::class)->only(['index','show']);
Route::post('movies/{movie}/react', [ReactionController::class,'store'])->middleware('auth')->name('movies.react');

require __DIR__.'/auth.php';
