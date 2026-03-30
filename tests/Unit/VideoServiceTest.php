<?php

namespace Tests\Unit;

use App\Services\VideoStorageService\LocalVideoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function testVideoServiceReturnsPath(): void
    {
        $service = new LocalVideoService();
        $result = $service->getVideoUrl('https://example.com/video.mp4');
        $this->assertEquals('https://example.com/video.mp4', $result);
    }

    public function testVideoServiceReturnsNull(): void
    {
        $service = new LocalVideoService();
        $result = $service->getVideoUrl(null);
        $this->assertNull($result);
    }
}

