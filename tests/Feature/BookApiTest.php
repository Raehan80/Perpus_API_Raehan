<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_books(): void
    {
        $this->getJson('/api/books')->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_and_search_books(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Belitung Stories', 'author' => 'Author One']);
        Book::factory()->create(['title' => 'Island Novel', 'author' => 'Belitung Writer']);
        Book::factory()->create(['title' => 'Unrelated Book', 'author' => 'Other Author']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/books?search=Belitung&per_page=1&page=1');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_pagination_per_page_is_clamped_to_supported_limits(): void
    {
        $user = User::factory()->create();
        Book::factory(101)->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/books?per_page=500')
            ->assertOk()
            ->assertJsonCount(100, 'data')
            ->assertJsonPath('meta.per_page', 100)
            ->assertJsonPath('meta.last_page', 2);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/books?per_page=0')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.per_page', 1);
    }

    public function test_authenticated_user_can_create_books_and_validation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();
        $payload = [
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'isbn' => '9789793062792',
            'published_year' => 2005,
            'stock' => 10,
            'description' => 'Novel tentang anak-anak Belitung.',
        ];

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', $payload)
            ->assertCreated()
            ->assertJsonPath('data.isbn', $payload['isbn']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('isbn');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'author', 'isbn']);
    }

    public function test_authenticated_user_can_show_and_update_a_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Old Title']);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Old Title');

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/books/{$book->id}", ['title' => 'Updated Title'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_updating_a_book_rejects_an_isbn_used_by_another_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $otherBook = Book::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/books/{$book->id}", ['isbn' => $otherBook->isbn])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('isbn');
    }

    public function test_missing_books_return_not_found_for_read_update_and_delete(): void
    {
        $user = User::factory()->create();
        $missingBookId = 999999;

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/books/{$missingBookId}")
            ->assertNotFound();

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/books/{$missingBookId}", ['title' => 'Missing'])
            ->assertNotFound();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/books/{$missingBookId}")
            ->assertNotFound();
    }

    public function test_authenticated_user_can_delete_a_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Buku berhasil dihapus');

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
