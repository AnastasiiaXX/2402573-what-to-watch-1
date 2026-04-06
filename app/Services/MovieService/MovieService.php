<?php

namespace App\Services\MovieService;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Support\Facades\Cache;

class MovieService
{
    /**
     * @param MovieRepositoryInterface $repository
     */
    public function __construct(private MovieRepositoryInterface $repository)
    {
    }

    /**
     * @param string $imdbId
     * @return array|null
     */
    public function searchMovieById(string $imdbId): ?array
    {
        return $this->repository->searchMovieById($imdbId);
    }

    /**
     * Adds a new film and a genre attached to it
     * if it does not exist yet
     *
     * @param string $imdbId
     * @return void
     */
    public function updateFilmInfo(string $imdbId): void
    {
        $data = $this->repository->searchMovieById($imdbId);
        $film = Film::where('imdb_id', $imdbId)->first();
        if ($film && $data) {
            $film->update(array_merge($data, ['status' => 'on moderation']));
            $genreIds = [];
            foreach ($data['genre'] as $genreName) {
                $genre = Genre::firstOrCreate(['name' => trim($genreName)]);
                $genreIds[] = $genre->id;
            }
            $film->genres()->sync($genreIds);
            Cache::forget('promo');
            Cache::forget('genres');
        }
    }
}
