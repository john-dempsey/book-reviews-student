<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('image')) {
            // Store the upload on the 'public' disk, under storage/app/public/books
            // rather than storage/app/private - files on this disk are the ones the
            // storage:link symlink makes reachable over HTTP at all. putFile() picks
            // a random filename for us and returns the path it saved to, which is
            // what belongs in the column - never the uploaded file's own original name.
            $validated['image'] = Storage::disk('public')->putFile('books', $request->file('image'));
        }

        $book = Book::create($validated);

        return redirect()->route('books.show', $book);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        // Same rules as store() - a book still has to make sense the second
        // time it's saved, not just the first. validationRules() below is
        // what keeps that "same rules" true by construction rather than by
        // remembering to copy a change into both methods.
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::disk('public')->putFile('books', $request->file('image'));
        }

        $book->update($validated);

        return redirect()->route('books.show', $book);
    }

    /**
     * Validation rules shared by store() and update() - a book has to make
     * sense the same way whether it's being created or edited.
     */
    private function validationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'required|integer|digits:4',
            'isbn' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'edition_number' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
