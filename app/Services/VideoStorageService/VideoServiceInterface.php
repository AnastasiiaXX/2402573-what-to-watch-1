<?php

namespace App\Services\VideoStorageService;

interface VideoServiceInterface
{
    public function getVideoUrl(?string $path): ?string;
}
