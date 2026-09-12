<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Seed the reviews table.
     *
     * Reviews are the weakest entity of all - each one belongs to both a
     * book and a user - so this must run after BookSeeder and
     * UserSeeder.
     *
     * Every book gets between 0 and 5 reviews, one per user at most
     * (the reviews table has a unique index on book_id + user_id, so a
     * user can't review the same book twice), and at least two books end
     * up with no reviews at all. The counts below always add up to 60;
     * shuffling them means it isn't always the same books left without
     * reviews.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $reviewCounts = [
            0, 0,
            5, 5, 5, 5,
            4, 4, 4, 4,
            3, 3, 3, 3,
            2, 2, 2, 2, 2, 2,
        ];
        shuffle($reviewCounts);

        $books->each(function (Book $book, int $index) use ($users, $reviewCounts) {
            $reviewerCount = $reviewCounts[$index];

            if ($reviewerCount === 0) {
                return;
            }

            $users->random($reviewerCount)->each(function (User $user) use ($book) {
                Review::factory()->create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}
