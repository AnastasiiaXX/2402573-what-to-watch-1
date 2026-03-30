<?php

namespace App\Console\Commands;

use App\Jobs\SyncCommentsJob;
use App\Models\Film;
use Illuminate\Console\Command;

class SyncCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $films = Film::where('status', 'ready')->get();
        foreach ($films as $film) {
            SyncCommentsJob::dispatch($film);
        }
    }
}
