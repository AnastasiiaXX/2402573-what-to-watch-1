<?php

namespace App\Jobs;

use App\Models\Film;
use App\Services\LoadCommentsService\LoadCommentsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncCommentsJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    /**
     * Create a new job instance.
     * @param Film $film
     */
    public function __construct(private Film $film)
    {
    }

    /**
     * Execute the job.
     *
     * @param LoadCommentsService $commentsService
     */
    public function handle(LoadCommentsService $commentsService): void
    {
        $commentsService->syncComments($this->film);
    }

    /**
     * Logs Job error
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        \Log::error("SyncingJob failed for film {$this->film->id}", [
            'error' => $exception->getMessage(),
            ]);
    }
}
