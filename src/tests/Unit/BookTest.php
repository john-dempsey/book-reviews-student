<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_book_can_be_created(): void
    {
        $book = Book::create([
            'title' => 'The Pragmatic Programmer',
            'year' => 1999,
        ]);

        $this->assertDatabaseHas('books', ['title' => 'The Pragmatic Programmer']);
        $this->assertSame(1999, $book->year);
    }

    public function test_a_book_has_many_authors(): void
    {
        $book = Book::create(['title' => 'Clean Code', 'year' => 2008]);
        $author = Author::create(['name' => 'Robert C. Martin']);

        $book->authors()->attach($author);

        $this->assertCount(1, $book->authors);
        $this->assertTrue($book->authors->first()->is($author));
    }

    public function test_a_book_has_many_reviews(): void
    {
        $book = Book::create(['title' => 'Refactoring', 'year' => 1999]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $book->reviews()->create([
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        $this->assertCount(1, $book->reviews);
    }

    public function test_the_factory_creates_a_valid_book(): void
    {
        $book = Book::factory()->create();

        $this->assertDatabaseHas('books', ['id' => $book->id]);
        $this->assertNotEmpty($book->title);
        $this->assertGreaterThanOrEqual(1950, $book->year);
    }

    public function test_the_search_scope_matches_a_books_title_an_authors_name_or_its_year(): void
    {
        $book = Book::factory()->create(['title' => 'Dune', 'year' => 1965]);
        $book->authors()->attach(Author::factory()->create(['name' => 'Frank Herbert']));

        $this->assertTrue(Book::search('Dune')->exists());
        $this->assertTrue(Book::search('Herbert')->exists());
        $this->assertTrue(Book::search('1965')->exists());
    }

    public function test_the_search_scope_finds_nothing_for_a_non_matching_term(): void
    {
        Book::factory()->create(['title' => 'Dune', 'year' => 1965]);

        $this->assertFalse(Book::search('zzz')->exists());
    }

    public function test_the_search_scope_with_a_blank_term_returns_every_book(): void
    {
        Book::factory()->count(3)->create();

        $this->assertSame(3, Book::search(null)->count());
        $this->assertSame(3, Book::search('')->count());
    }
}
