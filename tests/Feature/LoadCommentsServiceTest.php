<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Services\LoadCommentsService\LoadCommentsInterface;
use App\Services\LoadCommentsService\LoadCommentsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoadCommentsServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test comments service returns
     *  external source comments
     */
    public function testSyncCommentsSavesDataToDatabase(): void
    {
        $mockRepo = $this->mock(LoadCommentsInterface::class);

        $mockRepo->shouldReceive('getComments')
            ->once()
            ->andReturn([
                ['text' => 'Great movie!', 'rating' => 10],
                ['text' => 'Not bad', 'rating' => 7],
            ]);

        $film = Film::factory()->create(['imdb_id' => 'tt12345']);

        $service = app(LoadCommentsService::class);
        $service->syncComments($film);

        $this->assertDatabaseCount('comments', 2);
        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            'comment' => 'Great movie!',
            'rating' => 10
        ]);
    }
}
