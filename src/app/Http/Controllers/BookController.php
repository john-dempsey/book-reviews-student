<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $fillable already stops the wrong *keys* reaching the database;
        // this is what stops the wrong *values* - the same job the hand-
        // written validator class from the PHP module did, rule strings
        // instead of hand-written checks. A failure redirects back with
        // the errors and the submitted input attached automatically -
        // nothing here has to do that by hand.
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'required|integer|digits:4',
            'isbn' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'edition_number' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        $book = Book::create($validated);

        return redirect()->route('books.show', $book);
    }
}
