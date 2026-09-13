<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_author_can_be_created(): void
    {
        $author = Author::create(['name' => 'Andy Hunt']);

        $this->assertDatabaseHas('authors', ['name' => 'Andy Hunt']);
    }

    public function test_an_author_has_many_books(): void
    {
        $author = Author::create(['name' => 'Martin Fowler']);
        $book = Book::create(['title' => 'Patterns of Enterprise Application Architecture', 'year' => 2002]);

        $author->books()->attach($book);

        $this->assertCount(1, $author->books);
        $this->assertTrue($author->books->first()->is($book));
    }

    public function test_the_factory_creates_a_valid_author(): void
    {
        $author = Author::factory()->create();

        $this->assertDatabaseHas('authors', ['id' => $author->id]);
        $this->assertNotEmpty($author->name);
    }
}
