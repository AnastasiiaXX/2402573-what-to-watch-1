<?php

namespace App\Services\VideoStorageService;

class LocalVideoService implements VideoServiceInterface
{
    public function getVideoUrl(?string $path): ?string
    {
        return $path;
    }
}
