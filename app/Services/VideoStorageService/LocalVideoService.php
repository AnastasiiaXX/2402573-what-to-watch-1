<?php

namespace App\Services\VideoStorageService;

use Override;

class LocalVideoService implements VideoServiceInterface
{
    #[Override]
    public function getVideoUrl(?string $path): ?string
    {
        return $path;
    }
}
