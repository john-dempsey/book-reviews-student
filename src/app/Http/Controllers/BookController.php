<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager-load authors, and aggregate the review count/average rating
        // in the same query (withCount/withAvg), rather than loading every
        // review row just to print a number - avoids the N+1 problem without
        // pulling data the listing never displays.
        $books = Book::with('authors')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('title')
            ->paginate(12);

        return view('books.index', compact('books'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // Route model binding already resolved $book from {book} in the URL;
        // load its authors and reviews (plus each review's author) here,
        // rather than in the route, since not every route touching a Book
        // needs this much loaded.
        $book->load(['authors', 'reviews.user']);

        return view('books.show', compact('book'));
    }
}
