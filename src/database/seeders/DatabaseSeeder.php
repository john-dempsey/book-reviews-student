<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Order matters: the strong entities (users, authors) are seeded
     * first, since the weak entities that follow (books, then reviews)
     * depend on rows already existing to attach to - a book needs an
     * author, and a review needs both a book and a user.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
