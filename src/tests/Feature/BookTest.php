<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('books.index'));

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_the_index_page_lists_books(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Dune']);

        $response = $this->actingAs($user)->get(route('books.index'));

        $response->assertOk();
        $response->assertSee('Dune');
    }

    public function test_the_show_page_displays_a_books_authors_and_reviews(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Dune']);
        $book->authors()->attach(Author::factory()->create(['name' => 'Frank Herbert']));
        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'comment' => 'A genuine classic.',
        ]);

        $response = $this->actingAs($user)->get(route('books.show', $book));

        $response->assertOk();
        $response->assertSee('Frank Herbert');
        $response->assertSee('A genuine classic.');
    }

    public function test_a_nonexistent_book_returns_a_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/999');

        $response->assertNotFound();
    }
}
