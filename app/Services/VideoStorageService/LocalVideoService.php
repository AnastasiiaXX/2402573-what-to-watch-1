<?php

namespace App\Services\VideoStorageService;

use Override;

class LocalVideoService implements VideoServiceInterface
{
    /**
     * Gets the video from a local source
     *
     * @param string|null $path
     * @return string|null
     */
    #[Override]
    public function getVideoUrl(?string $path): ?string
    {
        return $path;
    }
}
