<?php

namespace App\Services\MovieService;

interface MovieRepositoryInterface
{
    /**
     * @param string $imdbId
     * @return array|null
     */
    public function searchMovieById(string $imdbId): ?array;
}
