<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Services\MovieViewCounterService;
use Illuminate\Console\Command;

class SyncMovieViewsToDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:sync-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store from redis to db';

    /**
     * Execute the console command.
     */
    public function handle(MovieViewCounterService $counter): int
{
    $views = $counter->pullAll();



    foreach ($views as $movieId => $count) {
        $movie = Movie::find($movieId);

        if ($movie) {
            $movie->views += $count;
            $movie->save();
            $this->info("Movie #{$movieId} +{$count} views saved.");
        }

        $counter->forget($movieId);
    }

    return Command::SUCCESS;
}

}
