<?php

namespace App\Jobs;

use App\Services\MovieService\MovieService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;

class UpdateFilmJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $imdbId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(MovieService $movieService): void
    {
        $movieService->updateFilmInfo($this->imdbId);
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
