<?php

namespace Tests\Feature;

use App\Jobs\UpdateFilmJob;
use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use App\Services\MovieService\MovieRepositoryInterface;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateFilmJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing adding a job to the queue
     */
    public function testUpdateJobPushedInQueue(): void
    {
        Queue::fake();

        $this->seed(RoleSeeder::class);
        $role = Role::where('name', 'moderator')->first();

        $moderator = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($moderator)
            ->post("/api/films", ['imdb_id' => 'tt111198']);
        Queue::assertPushed(UpdateFilmJob::class);
    }

    /**
     * Testing checking that a job data
     * updates database
     */
    public function testJobUpdatesDatabase(): void
    {
        $film = Film::factory()->create();
        $this->mock(MovieRepositoryInterface::class, function ($mock) use ($film) {
            $mock->shouldReceive('searchMovieById')
                ->with($film->imdb_id)
                ->andReturn(['name' => 'Test Name',
                    'description' => 'Test Description',
                    'genre' => ['Drama'],
                    'released' => 2003,
                    'director' => 'Test Director',
                    'run_time' => 90,
                    'starring' => ['Actor 1', 'Actor 2', 'Actor 3'],
                    'imdb_id' => 'tt11156',
                    'poster_image' => 'https://testlink.org'
                ]);
        });
        UpdateFilmJob::dispatchSync($film->imdb_id);
        $this->assertDatabaseHas('films', [
            'imdb_id' => 'tt11156',
            'name' => 'Test Name',
            'status' => 'on moderation'
        ]);
    }
}
