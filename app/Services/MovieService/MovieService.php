final <?php

namespace App\Services\MovieService;

use App\Models\Film;
use App\Models\Genre;

class MovieService
{


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
        }
    }
}
