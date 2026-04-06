<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Http\Responses\PaginateResponse;
use App\Http\Responses\SuccessResponse;
use App\Jobs\UpdateFilmJob;
use App\Models\Film;
use App\Models\Genre;
use App\Services\VideoStorageService\VideoServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class FilmsController extends Controller
{
    public function __construct(private VideoServiceInterface $videoService)
    {
    }

    /**
     * Gets all films using filters and sorting
     *
     * @param Request $request
     * @return PaginateResponse
     */
    public function index(Request $request): PaginateResponse
    {
        $status = $request->query('status', 'ready');
        $sortOrder = $request->query('order_to', 'desc');
        $sortRule = $request->query('order_by', 'released');
        $genre = $request->query('genre', null);
        $films = Film::where('status', $status);
        $user = auth()->user();

        if ($genre) {
            $genreName = Genre::where('name', $genre)->first();
            $films = $films->whereAttachedTo($genreName);
        }
        $favouriteIds = $user ? $user->favoriteFilms()->pluck('film_id')->toArray() : [];
        $films = $films->withAvg('comments as rating_avg', 'rating')
                        ->withCount('comments as scores_count')
                        ->orderBy($sortRule, $sortOrder)
                        ->with('genres')
                        ->paginate(8);
        foreach ($films as $film) {
            $film->is_favourite = in_array($film->id, $favouriteIds);
        }

        return new PaginateResponse($films, 200);
    }

    /**
     * Gets film by id
     *
     * @param Film $film
     * @return SuccessResponse
     */
    public function show(Film $film): SuccessResponse
    {
        $user = auth()->user();
        if ($user) {
            $film->is_favourite = $user->favoriteFilms()->where('film_id', $film->id)->exists();
        }

        $film->loadAvg('comments as rating_avg', 'rating')
            ->loadCount('comments as scores_count')
            ->load('genres');

        $film->video_link = $this->videoService->getVideoUrl($film->video_link);
        $film->preview_video_link = $this->videoService->getVideoUrl($film->preview_video_link);

        return new SuccessResponse($film, 200);
    }

    /**
     * Adds film (moderator only)
     *
     * @param StoreFilmRequest $request
     * @return SuccessResponse
     */
    public function store(StoreFilmRequest $request): SuccessResponse
    {
        /** @var array $validated */
        $validated = $request->validated();
        $newFilm = Film::create([...$validated, 'status' => 'pending']);
        UpdatefilmJob::dispatch($validated['imdb_id']);
        return new SuccessResponse($newFilm, 201);
    }

    /**
     * Updates film (moderator only)
     *
     * @param UpdateFilmRequest $request
     * @param Film $film
     * @return SuccessResponse
     */
    public function update(UpdateFilmRequest $request, Film $film): SuccessResponse
    {
        $validated = $request->validated();
         $film->update($validated);
        return new SuccessResponse($film, 200);
    }

    /**
     * Shows 4 similar films
     *
     * * @param Film $film
     * @return SuccessResponse
     */
    public function indexSimilar(Film $film): SuccessResponse
    {
        $genres = $film->genres;
        /** @var \Illuminate\Database\Eloquent\Collection $genres */

        if ($genres->isEmpty()) {
            return new SuccessResponse(collect([]), 200);
        }

        $similar = Film::whereAttachedTo($genres)
                        ->where('id', '!=', $film->id)
                        ->withAvg('comments as rating_avg', 'rating')
                        ->withCount('comments as scores_count')
                        ->limit(4)
                        ->get();
        return new SuccessResponse($similar, 200);
    }

    /**
     * Shows currently promoted film
     *
     * @return SuccessResponse
     */
    public function showPromo(): SuccessResponse
    {
        $promo = Cache::remember('promo', 3600, function () {
            return Film::where('is_promo', true)->first();
        });

        if (!$promo) {
            abort(404);
        }

        $promo->loadAvg('comments as rating_avg', 'rating')
            ->loadCount('comments as scores_count')
            ->load('genres');

        $promo->video_link = $this->videoService->getVideoUrl($promo->video_link);
        $promo->preview_video_link = $this->videoService->getVideoUrl($promo->preview_video_link);

        return new SuccessResponse($promo, 200);
    }

    /**
     * Adds promo field to a film
     *
     * @param Film $film
     * @return SuccessResponse
     */
    public function storePromo(Film $film): SuccessResponse
    {
        $film->update(['is_promo' => true]);
        Cache::forget('promo');
        return new SuccessResponse($film, 200);
    }
}
