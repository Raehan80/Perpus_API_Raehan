<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Registrasi berhasil')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'budi@example.com');

        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
    }

    public function test_registration_rejects_duplicate_email_and_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'budi@example.com']);

        $this->postJson('/api/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'different123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_can_log_in_and_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'budi@example.com',
            'password' => 'secret123',
        ]);

        $this->postJson('/api/login', [
            'email' => 'budi@example.com',
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Login berhasil')
            ->assertJsonPath('user.email', 'budi@example.com')
            ->assertJsonStructure(['token', 'token_type']);

        $this->postJson('/api/login', [
            'email' => 'budi@example.com',
            'password' => 'wrong-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_user_can_log_out_and_token_cannot_access_protected_routes_afterward(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout berhasil');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => explode('|', $token, 2)[0],
        ]);

        $this->app['auth']->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/books')
            ->assertUnauthorized();
    }
}
