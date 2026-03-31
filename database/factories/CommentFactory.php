<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'comment' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1, 10),
            'film_id' => Film::factory(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
