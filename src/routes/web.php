<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Browsing and adding to the catalogue - edit/update/destroy are still
    // to come, later in Lesson 3.
    Route::resource('books', BookController::class)->only(['index', 'show', 'create', 'store']);
    Route::resource('authors', AuthorController::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';
