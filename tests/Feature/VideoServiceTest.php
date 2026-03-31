<?php

namespace Tests\Feature;

use App\Services\VideoStorageService\LocalVideoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test service returns video link
     */
    public function testVideoServiceReturnsPath(): void
    {
        $service = new LocalVideoService();
        $result = $service->getVideoUrl('https://example.com/video.mp4');
        $this->assertEquals('https://example.com/video.mp4', $result);
    }

    /**
     * Test service returns null
     */
    public function testVideoServiceReturnsNull(): void
    {
        $service = new LocalVideoService();
        $result = $service->getVideoUrl(null);
        $this->assertNull($result);
    }
}
