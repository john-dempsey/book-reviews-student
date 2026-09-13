<?php

namespace App\Http\Controllers;

use App\Models\Author;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::withCount('books')
            ->orderBy('name')
            ->paginate(12);

        return view('authors.index', compact('authors'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        // Each book's own authors and review stats are loaded too, since
        // this page reuses the books.partials.card partial from the
        // catalogue listing - it expects the same data shape either way.
        $author->load(['books' => function ($query) {
            $query->with('authors')->withCount('reviews')->withAvg('reviews', 'rating');
        }]);

        return view('authors.show', compact('author'));
    }
}
