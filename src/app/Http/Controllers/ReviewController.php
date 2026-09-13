<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Show the form for leaving a review on the given book.
     */
    public function create(Book $book)
    {
        return view('reviews.create', compact('book'));
    }

    /**
     * Store a newly created review for the given book.
     */
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        // The migration's unique index on [book_id, user_id] already stops
        // a second row for this exact pair existing - this check exists so
        // trying anyway gets the same friendly redirect-back-with-errors
        // treatment as any other validation failure, rather than a raw
        // "Duplicate entry" database exception reaching the browser.
        if ($book->reviews()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors([
                'rating' => 'You have already reviewed this book.',
            ])->withInput();
        }

        $validated['user_id'] = auth()->id();
        $book->reviews()->create($validated);

        return redirect()->route('books.show', $book);
    }
}
