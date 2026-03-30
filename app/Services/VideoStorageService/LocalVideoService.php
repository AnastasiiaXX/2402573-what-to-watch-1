final <?php

namespace App\Services\VideoStorageService;

class LocalVideoService implements VideoServiceInterface
{
    #[\Override]
    public function getVideoUrl(?string $path): ?string
    {
        return $path;
    }
}
