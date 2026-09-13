<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_produces_the_expected_catalogue_shape(): void
    {
        $this->seed();

        $this->assertSame(5, User::count());
        $this->assertSame(5, Author::count());
        $this->assertSame(20, Book::count());
        $this->assertSame(60, Review::count());
    }

    public function test_seeding_produces_two_co_written_books(): void
    {
        $this->seed();

        $this->assertSame(2, Book::has('authors', '>=', 2)->count());
    }

    public function test_seeding_leaves_two_books_without_reviews(): void
    {
        $this->seed();

        $this->assertSame(2, Book::doesntHave('reviews')->count());
    }
}
