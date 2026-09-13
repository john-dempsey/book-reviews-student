<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
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
        $validated = $request->validate($this->validationRules());

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

    /**
     * Show the form for editing the given review.
     */
    public function edit(Review $review)
    {
        $this->authorizeOwner($review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the given review.
     */
    public function update(Request $request, Review $review)
    {
        $this->authorizeOwner($review);

        $review->update($request->validate($this->validationRules()));

        return redirect()->route('books.show', $review->book);
    }

    /**
     * Delete the given review.
     */
    public function destroy(Review $review)
    {
        $this->authorizeOwner($review);

        // $review->book has to be read before delete() - afterwards the
        // row (and the foreign key that made this relationship work) is
        // gone, and there'd be nothing left to redirect back to.
        $book = $review->book;
        $review->delete();

        return redirect()->route('books.show', $book);
    }

    /**
     * Validation rules shared by store() and update() - a review still has
     * to make sense the second time it's saved, not just the first.
     */
    private function validationRules(): array
    {
        return [
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ];
    }

    /**
     * Guard edit/update/destroy against anyone but the review's own author.
     * A stand-in for the Policy class Part 2 introduces - Laravel's actual
     * mechanism for exactly this kind of check - kept this plain for now
     * so that lesson isn't taught early by accident.
     */
    private function authorizeOwner(Review $review): void
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
