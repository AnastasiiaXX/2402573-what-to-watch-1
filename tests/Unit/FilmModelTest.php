<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmModelTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_returns_rating(): void
    {
        $film = Film::factory()->create();
        Comment::factory()->create(['film_id' => $film->id, 'rating' => 7]);
        Comment::factory()->create(['film_id' => $film->id, 'rating' => 8]);

        $this->assertEquals(7.5, $film->calculateRating());
    }
}
