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

    public function test_guests_are_redirected_to_login_from_my_reviews(): void
    {
        $response = $this->get(route('reviews.mine'));

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_my_reviews_lists_the_users_own_reviews(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Dune']);
        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'A genuine classic.',
        ]);

        $response = $this->actingAs($user)->get(route('reviews.mine'));

        $response->assertOk();
        $response->assertSee('Dune');
        $response->assertSee('A genuine classic.');
        $response->assertSee('5 / 5');
    }

    public function test_my_reviews_does_not_list_another_users_reviews(): void
    {
        $user = User::factory()->create();
        $someoneElse = User::factory()->create();
        Book::factory()->create(['title' => 'My Book'])
            ->reviews()->create(['user_id' => $user->id, 'rating' => 4]);
        Book::factory()->create(['title' => 'Someone Elses Book'])
            ->reviews()->create(['user_id' => $someoneElse->id, 'rating' => 2]);

        $response = $this->actingAs($user)->get(route('reviews.mine'));

        $response->assertSee('My Book');
        $response->assertDontSee('Someone Elses Book');
    }

    public function test_my_reviews_shows_the_most_recently_written_review_first(): void
    {
        $user = User::factory()->create();
        $older = Book::factory()->create(['title' => 'Older Review']);
        $newer = Book::factory()->create(['title' => 'Newer Review']);
        Review::factory()->create([
            'book_id' => $older->id,
            'user_id' => $user->id,
            'created_at' => now()->subDay(),
        ]);
        Review::factory()->create([
            'book_id' => $newer->id,
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('reviews.mine'));

        $response->assertSeeInOrder(['Newer Review', 'Older Review']);
    }

    public function test_my_reviews_shows_a_message_when_the_user_has_no_reviews(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reviews.mine'));

        $response->assertOk();
        $response->assertSee("You haven't reviewed any books yet.");
    }
}
