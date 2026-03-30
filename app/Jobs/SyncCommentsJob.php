<?php

namespace App\Jobs;

use App\Models\Film;
use App\Services\LoadCommentsService\LoadCommentsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SyncCommentsJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Film $film)
    {}

    /**
     * Execute the job.
     */
    public function handle(LoadCommentsService $commentsService): void
    {
        $commentsService->syncComments($this->film);
    }
}
