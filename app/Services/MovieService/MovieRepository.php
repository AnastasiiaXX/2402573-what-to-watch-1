<?php

namespace App\Services\MovieService;

use Override;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class MovieRepository implements MovieRepositoryInterface
{
    /**
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * Searches the movie in an external source
     * and parses the incoming data
     *
     * @param string $imdbId
     * @return array|null
     * @throws ClientExceptionInterface
     */
    #[Override]
    public function searchMovieById(string $imdbId): ?array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            "https://www.omdbapi.com?apikey=" . config("services.omdb.key") . "&i={$imdbId}"
        );
        $response = $this->httpClient->sendRequest($request);
        $omdbData = json_decode($response->getBody()->getContents(), true);
        if (empty($omdbData) || $omdbData['Response'] === 'False') {
            return null;
        }
        return [
            'name' => $omdbData['Title'],
            'description' => $omdbData['Plot'],
            'genre' => explode(',', $omdbData['Genre']),
            'released' => intval($omdbData['Year']),
            'director' => $omdbData['Director'],
            'run_time' => intval($omdbData['Runtime']),
            'starring' => explode(', ', $omdbData['Actors']),
            'imdb_id' => $omdbData['imdbID'],
            'poster_image' => $omdbData['Poster']
        ];
    }
}
