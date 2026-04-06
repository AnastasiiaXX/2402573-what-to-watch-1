<?php

namespace App\Services\LoadCommentsService;

interface LoadCommentsInterface
{
    /**
     * @param string $imdbId
     * @return array|null
     */
    public function getComments(string $imdbId): ?array;
}
