<?php

namespace App\Services\VideoStorageService;

interface VideoServiceInterface
{
    /**
     * @param string|null $path
     * @return string|null
     */
    public function getVideoUrl(?string $path): ?string;
}
