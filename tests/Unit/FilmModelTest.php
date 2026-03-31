<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test genre relation
     */
    public function testFilmBelongsToManyGenres(): void
    {
        $film = Film::factory()->create();
        $genre = Genre::factory()->create();

        $film->genres()->attach($genre);

        $this->assertTrue($film->genres->contains($genre));
        $this->assertInstanceOf(Genre::class, $film->genres->first());
    }

    /**
     * Test getting a film rating
     */
    public function testReturnsRating(): void
    {
        $film = Film::factory()->create();
        Comment::factory()->create(['film_id' => $film->id, 'rating' => 7]);
        Comment::factory()->create(['film_id' => $film->id, 'rating' => 8]);

        $this->assertEquals(7.5, $film->rating);
    }

    /**
     * Test getting a film rating
     * with no reviews
     */
    public function testReturnsRatingNullWithNoComments(): void
    {
        $film = Film::factory()->create();
        $this->assertNull($film->rating);
    }

    /**
     * Test scores count
     */
    public function testScoresCountReturnsCorrectCount(): void
    {
        $film = Film::factory()->create();
        Comment::factory()->count(3)->create(['film_id' => $film->id]);

        $this->assertEquals(3, $film->scoresCount);
    }

    /**
     * Test scores count with zero
     */
    public function testScoresCountReturnsZeroIfNoComments(): void
    {
        $film = Film::factory()->create();

        $this->assertEquals(0, $film->scoresCount);
    }
}
