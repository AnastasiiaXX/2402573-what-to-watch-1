<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Services\MovieService\MovieRepositoryInterface;
use App\Services\MovieService\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test services updates film info
     */
    public function testUpdateFilmInfoUpdatesStatusAndGenres(): void
    {
        $mockRepo = $this->mock(MovieRepositoryInterface::class);
        $mockRepo->shouldReceive('searchMovieById')
            ->once()
            ->andReturn([
                'name' => 'New Title',
                'genre' => ['Comedy', 'Action']
            ]);

        $film = Film::factory()->create(['imdb_id' => 'tt999', 'status' => 'pending']);

        $service = app(MovieService::class);
        $service->updateFilmInfo('tt999');

        $film->refresh();
        $this->assertEquals('on moderation', $film->status);
        $this->assertCount(2, $film->genres);
        $this->assertDatabaseHas('genres', ['name' => 'Comedy']);
    }
}
