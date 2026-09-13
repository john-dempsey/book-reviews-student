<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Seed the books table.
     *
     * Books are a weak entity here - a book is meaningless without at
     * least one author to attach to the author_book pivot - so this must
     * run after AuthorSeeder.
     */
    public function run(): void
    {
        $authors = Author::all();

        $books = Book::factory(20)->create();

        // Most books have a single author.
        $books->each(function (Book $book) use ($authors) {
            $book->authors()->attach($authors->random());
        });

        // A couple of books are co-written - attach a second, different
        // author to two randomly chosen books.
        $books->random(2)->each(function (Book $book) use ($authors) {
            $firstAuthorId = $book->authors->first()->id;

            $secondAuthor = $authors
                ->reject(fn (Author $author) => $author->id === $firstAuthorId)
                ->random();

            $book->authors()->attach($secondAuthor);
        });
    }
}
