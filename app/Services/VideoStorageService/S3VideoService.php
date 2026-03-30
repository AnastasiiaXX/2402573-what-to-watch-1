final <?php

namespace App\Services\VideoStorageService;

class S3VideoService implements VideoServiceInterface
{
    #[\Override]
    public function getVideoUrl(?string $path): ?string
    {
        return $path;
    }
}
