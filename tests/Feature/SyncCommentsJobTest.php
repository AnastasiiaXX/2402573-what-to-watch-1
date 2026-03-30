<?php

namespace Tests\Feature;

use App\Jobs\SyncCommentsJob;
use App\Models\Film;
use App\Services\LoadCommentsService\LoadCommentsInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncCommentsJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getComments called with the right movie
     */
    public function testJobWithTheRightFilm(): void
    {
        $film = Film::factory()->create();
        $this->mock(LoadCommentsInterface::class, function ($mock) use ($film) {
            $mock->shouldReceive('getComments')
                ->with($film->imdb_id)
                ->once();
        });
        SyncCommentsJob::dispatchSync($film);
    }

    /**
     * Test the job updates comments table
     */
    public function testJobUpdatesDatabase(): void
    {
        $film = Film::factory()->create();
        $this->mock(LoadCommentsInterface::class, function ($mock) use ($film)
        {
            $mock->shouldReceive('getComments')
                ->with($film->imdb_id)
                ->andReturn([
                    ['text' => 'Great movie!', 'rating' => 8],
                ]);
        });
        SyncCommentsJob::dispatchSync($film);
        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            'comment' => 'Great movie!',
            'rating' => 8,
            'user_id' => null,
        ]);
    }
}
