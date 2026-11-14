<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_does_not_create_records_in_production(): void
    {
        $this->app['env'] = 'production';

        app(DatabaseSeeder::class)->run();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('books', 0);
    }

    public function test_local_demo_seeder_uses_configured_admin_credentials_only(): void
    {
        $this->app['config']->set('app.env', 'local');
        $this->app['config']->set('seeding.admin_email', null);
        $this->app['config']->set('seeding.admin_password', null);

        app(DatabaseSeeder::class)->run();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('books', 20);
    }

    public function test_local_demo_seeder_creates_admin_with_configured_credentials(): void
    {
        $this->app['config']->set('app.env', 'local');
        $this->app['config']->set('seeding.admin_email', 'local-admin@example.com');
        $this->app['config']->set('seeding.admin_password', 'change-this-local-password');

        app(DatabaseSeeder::class)->run();

        $this->assertDatabaseHas('users', ['email' => 'local-admin@example.com']);
        $this->assertDatabaseCount('books', 20);
        $this->assertTrue(
            Hash::check(
                'change-this-local-password',
                User::where('email', 'local-admin@example.com')->firstOrFail()->getAuthPassword()
            )
        );
    }

    public function test_local_demo_seeder_rotates_an_existing_admin_password(): void
    {
        User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $this->app['config']->set('app.env', 'local');
        $this->app['config']->set('seeding.admin_email', 'admin@example.com');
        $this->app['config']->set('seeding.admin_password', 'new-local-admin-password');

        app(DatabaseSeeder::class)->run();
        app(DatabaseSeeder::class)->run();

        $this->assertTrue(
            Hash::check(
                'new-local-admin-password',
                User::where('email', 'admin@example.com')->firstOrFail()->getAuthPassword()
            )
        );
        $this->assertDatabaseCount('books', 20);
    }
}
