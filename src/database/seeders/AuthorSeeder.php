<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Seed the authors table.
     */
    public function run(): void
    {
        Author::factory(5)->create();
    }
}
