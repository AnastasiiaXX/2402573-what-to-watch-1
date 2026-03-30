<?php

namespace App\Services\LoadCommentsService;

interface LoadCommentsInterface
{
    public function getComments(string $imdbId): ?array;
}
