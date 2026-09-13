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
}
