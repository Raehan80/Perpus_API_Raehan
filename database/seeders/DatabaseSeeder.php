<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Demo seeding is disabled outside local and testing environments.');

            return;
        }

        $email = config('seeding.admin_email');
        $password = config('seeding.admin_password');

        if (filled($email) && filled($password)) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Admin Perpustakaan',
                    'password' => $password,
                ]
            );
        } else {
            $this->command?->warn('Demo admin skipped. Set SEED_ADMIN_EMAIL and SEED_ADMIN_PASSWORD to create it.');
        }

        if (! Book::query()->exists()) {
            Book::factory(20)->create();
        }
    }
}
