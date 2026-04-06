<?php

namespace App\Services\VideoStorageService;

use Override;

class S3VideoService implements VideoServiceInterface
{
    /**
     * Gets the video from Amazon S3 Service
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
