<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\PaginateResponse;
use App\Http\Responses\SuccessResponse;
use App\Jobs\UpdateFilmJob;
use App\Models\Film;
use App\Models\Genre;
use App\Services\VideoStorageService\VideoServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FilmsController extends Controller
{
    public function __construct(private VideoServiceInterface $videoService) {}

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
        $films = $films->orderBy($sortRule, $sortOrder)->with('genres')->paginate(8);
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
        $film->video_link = $this->videoService->getVideoUrl($film->video_link);
        $film->preview_video_link = $this->videoService->getVideoUrl($film->preview_video_link);
        return new SuccessResponse($film->load('genres'), 200);
    }

    /**
     * Adds film (moderator only)
     *
     * @param Request $request
     * @return SuccessResponse
     */
    public function store(Request $request): SuccessResponse
    {
        $validated = $request->validate(['imdb_id' => ['required', 'string', 'unique:films', 'regex:/^tt\d+$/']]);
        $newFilm = Film::create([...$validated, 'status' => 'pending']);
        UpdatefilmJob::dispatch($validated['imdb_id']);
        return new SuccessResponse($newFilm, 201);
    }

    /**
     * Updates film (moderator only)
     *
     * @param Request $request
     * @param Film $film
     * @return SuccessResponse
     */
    public function update(Request $request, Film $film): SuccessResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'poster_image' => ['string', 'max:255'],
            'preview_image' => ['string', 'max:255'],
            'background_image' => ['string', 'max:255'],
            'background_color' => ['string', 'max:9'],
            'video_link' => ['string', 'max:255'],
            'preview_video_link' => ['string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'director' => ['string', 'max:255'],
            'starring' => ['array'],
            'genre' => ['array'],
            'released' => ['integer'],
            'run_time' => ['integer'],
            'imdb_id' => ['string', Rule::unique('films')->ignore($film->id), 'regex:/^tt\d+$/', 'required'],
            'status' => ['required','string', Rule::in(['ready', 'pending', 'on moderation'])],
        ]);
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

        $similar = Film::whereAttachedTo($genres)->where('id', '!=', $film->id)
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
        $promo = Film::where('is_promo', true)->first();
        $promo->video_link = $this->videoService->getVideoUrl($promo->video_link);
        $promo->preview_video_link = $this->videoService->getVideoUrl($promo->preview_video_link);

        return new SuccessResponse($promo->load('genres'), 200);
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
        return new SuccessResponse($film, 200);
    }
}
