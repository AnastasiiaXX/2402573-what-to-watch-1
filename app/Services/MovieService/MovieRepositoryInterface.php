<?php

namespace App\Services\MovieService;

interface MovieRepositoryInterface
{
    public function searchMovieById(string $imdbId): ?array;
}
