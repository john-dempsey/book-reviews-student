<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('authors.index'));

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_the_index_page_lists_authors(): void
    {
        $user = User::factory()->create();
        Author::factory()->create(['name' => 'Frank Herbert']);

        $response = $this->actingAs($user)->get(route('authors.index'));

        $response->assertOk();
        $response->assertSee('Frank Herbert');
    }

    public function test_the_show_page_displays_an_authors_books(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create(['name' => 'Frank Herbert']);
        $author->books()->attach(Book::factory()->create(['title' => 'Dune']));

        $response = $this->actingAs($user)->get(route('authors.show', $author));

        $response->assertOk();
        $response->assertSee('Dune');
    }

    public function test_a_nonexistent_author_returns_a_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/authors/999');

        $response->assertNotFound();
    }
}
