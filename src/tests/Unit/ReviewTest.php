<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_review_can_be_created(): void
    {
        $book = $this->makeBook();
        $user = $this->makeUser();

        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 4,
            'comment' => 'Solid read.',
        ]);

        $this->assertDatabaseHas('reviews', ['rating' => 4, 'comment' => 'Solid read.']);
    }

    public function test_a_review_belongs_to_a_book(): void
    {
        $book = $this->makeBook();
        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $this->makeUser()->id,
            'rating' => 5,
        ]);

        $this->assertTrue($review->book->is($book));
    }

    public function test_a_review_belongs_to_a_user(): void
    {
        $user = $this->makeUser();
        $review = Review::create([
            'book_id' => $this->makeBook()->id,
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        $this->assertTrue($review->user->is($user));
    }

    public function test_a_user_can_have_many_reviews(): void
    {
        $user = $this->makeUser();
        Review::create(['book_id' => $this->makeBook()->id, 'user_id' => $user->id, 'rating' => 3]);
        Review::create(['book_id' => $this->makeBook()->id, 'user_id' => $user->id, 'rating' => 4]);

        $this->assertCount(2, $user->reviews);
    }

    public function test_a_user_cannot_review_the_same_book_twice(): void
    {
        $book = $this->makeBook();
        $user = $this->makeUser();

        Review::create(['book_id' => $book->id, 'user_id' => $user->id, 'rating' => 5]);

        $this->expectException(UniqueConstraintViolationException::class);

        Review::create(['book_id' => $book->id, 'user_id' => $user->id, 'rating' => 1]);
    }

    private function makeBook(): Book
    {
        return Book::create(['title' => 'Test Book', 'year' => 2000]);
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}
