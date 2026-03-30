final <?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Favourite;
use App\Models\Film;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * Gets favourite films for a user
     *
     * @return SuccessResponse
     */
    public function index(): SuccessResponse
    {
        $user = auth()->user();
        $favorites = $user->favoriteFilms()->with('genres')->get();
        foreach ($favorites as $film) {
            $film->is_favourite = true;
        }
        return new SuccessResponse($favorites, 200);
    }

    /**
     * Adds a film to favourites
     *
     * @param Film $film
     * @return SuccessResponse|ErrorResponse
     */
    public function store(Film $film): SuccessResponse|ErrorResponse
    {
        $user = auth()->user();
        if ($user->favoriteFilms()->where('film_id', $film->id)->exists()) {
            return new ErrorResponse(422, 'Переданные данные не корректны.');
        }
        $user->favoriteFilms()->attach($film);
        $film->is_favourite = true;

        return new SuccessResponse($film->load('genres'), 200);
    }

    /**
     * Deletes a film from favourites
     *
     * @param Film $film
     * @return SuccessResponse|ErrorResponse
     */
    public function destroy(Film $film): SuccessResponse|ErrorResponse
    {
        $user = auth()->user();
        if (!$user->favoriteFilms()->where('film_id', $film->id)->exists()) {
            return new ErrorResponse(422, 'Переданные данные не корректны.');
        }
        $user->favoriteFilms()->detach($film);
        return new SuccessResponse([], 200);
    }
}
