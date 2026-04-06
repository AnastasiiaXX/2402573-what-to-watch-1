<?php

namespace App\Jobs;

use App\Services\MovieService\MovieService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Throwable;

class UpdateFilmJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @param string $imdbId
     */
    public function __construct(private string $imdbId)
    {
    }

    /**
     * Execute the job.
     *
     * @param MovieService $movieService
     */
    public function handle(MovieService $movieService): void
    {
        \Log::info("Updating film data from API", ['imdb_id' => $this->imdbId]);

        $movieService->updateFilmInfo($this->imdbId);
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        \Log::error("UpdatingJob failed for film {$this->imdbId}", [
                'error' => $exception->getMessage(),
            ]);
    }

    /**
     * Links limiter with the job
     *
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('movie-api')];
    }
}
