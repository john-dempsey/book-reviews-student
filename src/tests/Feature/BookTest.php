<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_a_book_can_be_created(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'Project Hail Mary',
            'year' => 2021,
            'image' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $book = Book::firstWhere('title', 'Project Hail Mary');
        $response->assertRedirect(route('books.show', $book));
        $this->assertNotNull($book);
        Storage::disk('public')->assertExists($book->image);
    }

    public function test_creating_a_book_without_a_title_fails_validation_and_repopulates_the_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('books.create'))
            ->post(route('books.store'), [
                'title' => '',
                'year' => 2021,
            ]);

        $response->assertRedirect(route('books.create'));
        $response->assertSessionHasErrors('title');
        $response->assertSessionHasInput('year', 2021);
        $this->assertDatabaseMissing('books', ['year' => 2021]);
    }

    public function test_a_book_can_be_updated(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Old Title']);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => 'New Title',
            'year' => $book->year,
        ]);

        $response->assertRedirect(route('books.show', $book));
        $this->assertSame('New Title', $book->fresh()->title);
    }

    public function test_updating_a_book_without_a_title_fails_validation_and_leaves_it_unchanged(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Dune']);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '',
            'year' => $book->year,
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertSame('Dune', $book->fresh()->title);
    }

    public function test_a_book_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertModelMissing($book);
    }

    public function test_deleting_a_book_removes_its_cover_image_from_storage(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('cover.jpg')->store('books', 'public');
        $book = Book::factory()->create(['image' => $path]);

        $this->actingAs($user)->delete(route('books.destroy', $book));

        Storage::disk('public')->assertMissing($path);
    }

    public function test_the_index_page_search_matches_a_books_title(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Dune']);
        Book::factory()->create(['title' => 'Some Other Title']);

        $response = $this->actingAs($user)->get(route('books.index', ['search' => 'Dune']));

        $response->assertOk();
        $response->assertSee('Dune');
        $response->assertDontSee('Some Other Title');
    }

    public function test_the_index_page_search_matches_an_authors_name(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Dune']);
        $book->authors()->attach(Author::factory()->create(['name' => 'Frank Herbert']));
        Book::factory()->create(['title' => 'Some Other Title']);

        $response = $this->actingAs($user)->get(route('books.index', ['search' => 'Herbert']));

        $response->assertOk();
        $response->assertSee('Dune');
        $response->assertDontSee('Some Other Title');
    }

    public function test_the_index_page_search_matches_a_year(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Dune', 'year' => 1965]);
        Book::factory()->create(['title' => 'Some Other Title', 'year' => 1999]);

        $response = $this->actingAs($user)->get(route('books.index', ['search' => '1965']));

        $response->assertOk();
        $response->assertSee('Dune');
        $response->assertDontSee('Some Other Title');
    }

    public function test_the_index_page_shows_a_message_for_a_non_matching_search(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Dune']);

        $response = $this->actingAs($user)->get(route('books.index', ['search' => 'zzz']));

        $response->assertOk();
        $response->assertDontSee('Dune');
        $response->assertSee('No books match "zzz".');
    }

    public function test_the_search_box_repopulates_with_the_current_search_term(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Dune']);

        $response = $this->actingAs($user)->get(route('books.index', ['search' => 'Dune']));

        $response->assertSee('value="Dune"', false);
    }

    public function test_the_full_create_edit_delete_flow(): void
    {
        $user = User::factory()->create();

        $createResponse = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'The Fellowship of the Ring',
            'year' => 1954,
        ]);
        $book = Book::firstWhere('title', 'The Fellowship of the Ring');
        $createResponse->assertRedirect(route('books.show', $book));

        $editResponse = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => 'The Fellowship of the Ring (Revised)',
            'year' => 1954,
        ]);
        $editResponse->assertRedirect(route('books.show', $book));
        $this->assertSame('The Fellowship of the Ring (Revised)', $book->fresh()->title);

        $deleteResponse = $this->actingAs($user)->delete(route('books.destroy', $book));
        $deleteResponse->assertRedirect(route('books.index'));
        $this->assertModelMissing($book);
    }
}
