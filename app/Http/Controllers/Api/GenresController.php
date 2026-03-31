<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use App\Models\Genre;
use App\Http\Requests\UpdateGenreRequest;
use Illuminate\Support\Facades\Cache;

class GenresController extends Controller
{
    /**
     * Gets all genres and returns response
     *
     * @return SuccessResponse
     */
    public function index(): SuccessResponse
    {
        $genres = Cache::remember('genres', 3600, function () {
            return Genre::all();
        });
        return new SuccessResponse($genres, 200);
    }

    /**
     * Updates a genre
     *
     * @param UpdateGenreRequest $request
     * @param Genre $genre
     *
     * @return SuccessResponse
     */
    public function update(UpdateGenreRequest $request, Genre $genre): SuccessResponse
    {
        $validated = $request->validated();
        $genre->update($validated);
        Cache::forget('genres');
        return new SuccessResponse($genre, 200);
    }
}
