<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_books_index_page_runs_the_same_number_of_queries_regardless_of_catalogue_size(): void
    {
        $user = User::factory()->create();

        $this->seedBooks(3);
        $withThreeBooks = $this->countQueries(fn () => $this->actingAs($user)->get(route('books.index')));

        $this->seedBooks(12); // 15 books total now
        $withFifteenBooks = $this->countQueries(fn () => $this->actingAs($user)->get(route('books.index')));

        $this->assertSame($withThreeBooks, $withFifteenBooks);
    }

    public function test_the_books_show_page_runs_the_same_number_of_queries_regardless_of_review_count(): void
    {
        $user = User::factory()->create();

        $fewReviews = Book::factory()->create();
        $fewReviews->authors()->attach(Author::factory()->count(2)->create());
        Review::factory()->count(3)->create(['book_id' => $fewReviews->id]);
        $withThreeReviews = $this->countQueries(fn () => $this->actingAs($user)->get(route('books.show', $fewReviews)));

        $manyReviews = Book::factory()->create();
        $manyReviews->authors()->attach(Author::factory()->count(2)->create());
        Review::factory()->count(15)->create(['book_id' => $manyReviews->id]);
        $withFifteenReviews = $this->countQueries(fn () => $this->actingAs($user)->get(route('books.show', $manyReviews)));

        $this->assertSame($withThreeReviews, $withFifteenReviews);
    }

    public function test_the_authors_index_page_runs_the_same_number_of_queries_regardless_of_author_count(): void
    {
        $user = User::factory()->create();

        Author::factory()->count(3)->create();
        $withThreeAuthors = $this->countQueries(fn () => $this->actingAs($user)->get(route('authors.index')));

        Author::factory()->count(12)->create(); // 15 authors total now
        $withFifteenAuthors = $this->countQueries(fn () => $this->actingAs($user)->get(route('authors.index')));

        $this->assertSame($withThreeAuthors, $withFifteenAuthors);
    }

    public function test_the_authors_show_page_runs_the_same_number_of_queries_regardless_of_book_count(): void
    {
        $user = User::factory()->create();

        $fewBooks = Author::factory()->create();
        Book::factory()->count(3)->create()->each(fn (Book $book) => $book->authors()->attach($fewBooks));
        $withThreeBooks = $this->countQueries(fn () => $this->actingAs($user)->get(route('authors.show', $fewBooks)));

        $manyBooks = Author::factory()->create();
        Book::factory()->count(15)->create()->each(fn (Book $book) => $book->authors()->attach($manyBooks));
        $withFifteenBooks = $this->countQueries(fn () => $this->actingAs($user)->get(route('authors.show', $manyBooks)));

        $this->assertSame($withThreeBooks, $withFifteenBooks);
    }

    public function test_the_my_reviews_page_runs_the_same_number_of_queries_regardless_of_review_count(): void
    {
        $fewReviews = User::factory()->create();
        Review::factory()->count(3)->create(['user_id' => $fewReviews->id]);
        $withThreeReviews = $this->countQueries(fn () => $this->actingAs($fewReviews)->get(route('reviews.mine')));

        $manyReviews = User::factory()->create();
        Review::factory()->count(15)->create(['user_id' => $manyReviews->id]);
        $withFifteenReviews = $this->countQueries(fn () => $this->actingAs($manyReviews)->get(route('reviews.mine')));

        $this->assertSame($withThreeReviews, $withFifteenReviews);
    }

    /**
     * Create $count books, each with two shared authors and two reviews -
     * enough relationships attached per book that an N+1 anywhere in the
     * chain would show up as a query count that grows with $count.
     */
    private function seedBooks(int $count): void
    {
        $authors = Author::factory()->count(2)->create();

        Book::factory()->count($count)->create()->each(function (Book $book) use ($authors) {
            $book->authors()->attach($authors);
            Review::factory()->count(2)->create(['book_id' => $book->id]);
        });
    }

    /**
     * Run $fn and return exactly how many database queries it issued.
     */
    private function countQueries(callable $fn): int
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        $fn();

        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }
}
