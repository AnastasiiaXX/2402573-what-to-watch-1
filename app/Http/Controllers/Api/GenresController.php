<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

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
     * @param Request $request
     * @param Genre $genre
     *
     * @return SuccessResponse
     */
    public function update(Request $request, Genre $genre): SuccessResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', Rule::unique('genres')->ignore($genre->id)]]);
        $genre->update($validated);
        Cache::forget('genres');
        return new SuccessResponse($genre, 200);
    }
}
