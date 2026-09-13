<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(random_int(2, 5)), '.'),
            'description' => fake()->paragraphs(3, true),
            'year' => fake()->numberBetween(1950, (int) date('Y')),
            'image' => null,
            'isbn' => fake()->isbn13(),
            'publisher' => fake()->company(),
            'edition_number' => (string) fake()->numberBetween(1, 5),
            'price' => fake()->randomFloat(2, 9.99, 79.99),
        ];
    }
}
