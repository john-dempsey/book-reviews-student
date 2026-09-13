<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
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

    // Full CRUD on the catalogue.
    Route::resource('books', BookController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('authors', AuthorController::class)->only(['index', 'show']);

    // Nested under its book - a review only ever makes sense in the
    // context of one - so the URL says so: /books/{book}/reviews/create,
    // /books/{book}/reviews.
    Route::resource('books.reviews', ReviewController::class)->only(['create', 'store']);
});

require __DIR__.'/auth.php';
