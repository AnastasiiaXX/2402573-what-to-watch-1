<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factofinal ry;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'status' => 'ready',
            'poster_image' => fake()->imageUrl(),
            'preview_image' => fake()->imageUrl(),
            'background_image' => fake()->imageUrl(),
            'background_color' => fake()->hexColor(),
            'description' => fake()->paragraph(),
            'video_link' => fake()->url(),
            'preview_video_link' => fake()->url(),
            'director' => fake()->name(),
            'starring' => fake()->words(6),
            'run_time' => fake()->numberBetween(5, 400),
            'released' => fake()->year(),
            'imdb_id' => 'tt' . fake()->unique()->numberBetween(100000, 999999),
            'is_promo' => true
        ];
    }
}
