<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_review_can_be_created(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('books.reviews.store', $book), [
            'rating' => 4,
            'comment' => 'Solid read.',
        ]);

        $review = Review::firstWhere(['book_id' => $book->id, 'user_id' => $user->id]);
        $response->assertRedirect(route('books.show', $book));
        $this->assertNotNull($review);
        $this->assertSame(4, $review->rating);
    }

    public function test_creating_a_review_without_a_rating_fails_validation_and_repopulates_the_form(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('books.reviews.create', $book))
            ->post(route('books.reviews.store', $book), [
                'rating' => '',
                'comment' => 'Should not save.',
            ]);

        $response->assertRedirect(route('books.reviews.create', $book));
        $response->assertSessionHasErrors('rating');
        $response->assertSessionHasInput('comment', 'Should not save.');
        $this->assertDatabaseMissing('reviews', ['comment' => 'Should not save.']);
    }

    public function test_reviewing_the_same_book_twice_fails_with_a_friendly_error(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        Review::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('books.reviews.store', $book), [
            'rating' => 3,
        ]);

        $response->assertSessionHasErrors('rating');
        $this->assertCount(1, $book->reviews()->where('user_id', $user->id)->get());
    }

    public function test_a_review_can_be_updated_by_its_owner(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id, 'rating' => 2]);

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 5,
            'comment' => 'Changed my mind - loved it.',
        ]);

        $response->assertRedirect(route('books.show', $review->book));
        $this->assertSame(5, $review->fresh()->rating);
    }

    public function test_a_review_can_be_deleted_by_its_owner(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $book = $review->book;

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('books.show', $book));
        $this->assertModelMissing($review);
    }

    public function test_editing_someone_elses_review_is_forbidden(): void
    {
        $owner = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);
        $someoneElse = User::factory()->create();

        $response = $this->actingAs($someoneElse)->get(route('reviews.edit', $review));

        $response->assertForbidden();
    }

    public function test_updating_someone_elses_review_is_forbidden(): void
    {
        $owner = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id, 'rating' => 3]);
        $someoneElse = User::factory()->create();

        $response = $this->actingAs($someoneElse)->put(route('reviews.update', $review), [
            'rating' => 1,
        ]);

        $response->assertForbidden();
        $this->assertSame(3, $review->fresh()->rating);
    }

    public function test_deleting_someone_elses_review_is_forbidden(): void
    {
        $owner = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);
        $someoneElse = User::factory()->create();

        $response = $this->actingAs($someoneElse)->delete(route('reviews.destroy', $review));

        $response->assertForbidden();
        $this->assertModelExists($review);
    }

    public function test_the_full_write_edit_delete_flow_as_the_reviews_own_author(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $createResponse = $this->actingAs($user)->post(route('books.reviews.store', $book), [
            'rating' => 3,
            'comment' => 'Decent.',
        ]);
        $review = Review::firstWhere(['book_id' => $book->id, 'user_id' => $user->id]);
        $createResponse->assertRedirect(route('books.show', $book));

        $updateResponse = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 5,
            'comment' => 'Actually, loved it on a re-read.',
        ]);
        $updateResponse->assertRedirect(route('books.show', $book));
        $this->assertSame(5, $review->fresh()->rating);

        $deleteResponse = $this->actingAs($user)->delete(route('reviews.destroy', $review));
        $deleteResponse->assertRedirect(route('books.show', $book));
        $this->assertModelMissing($review);
    }
}
