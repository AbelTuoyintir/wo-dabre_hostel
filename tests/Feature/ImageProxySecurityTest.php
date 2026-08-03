<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageProxySecurityTest extends TestCase
{
    private string $testImagePath;
    private string $testTextPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a fake test image file inside public storage (using PNG magic bytes or a dummy file)
        $this->testImagePath = storage_path('app/public/security-test-temp.png');
        file_put_contents($this->testImagePath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

        // Create a fake non-media test text file inside public storage
        $this->testTextPath = storage_path('app/public/security-test-temp.txt');
        file_put_contents($this->testTextPath, 'Sensitive config or text data');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testImagePath)) {
            unlink($this->testImagePath);
        }
        if (file_exists($this->testTextPath)) {
            unlink($this->testTextPath);
        }

        parent::tearDown();
    }

    /**
     * Test that legitimate image files can be retrieved via /image?path=...
     */
    public function test_legitimate_images_can_be_retrieved(): void
    {
        $response = $this->get('/image?path=security-test-temp.png');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /**
     * Test that directory traversal attempts are blocked on /image proxy.
     */
    public function test_directory_traversal_attempts_are_blocked(): void
    {
        // Traversal with Unix style
        $responseUnix = $this->get('/image?path=../../.env');
        $responseUnix->assertStatus(403);

        // Traversal with Windows style
        $responseWin = $this->get('/image?path=..\\..\\.env');
        $responseWin->assertStatus(403);
    }

    /**
     * Test that encoded directory traversal attempts are blocked on /image proxy.
     */
    public function test_encoded_directory_traversal_attempts_are_blocked(): void
    {
        $response = $this->get('/image?path=..%2F..%2F.env');
        $response->assertStatus(403);
    }

    /**
     * Test that non-media files are rejected even if they are stored in the public directory.
     */
    public function test_non_media_files_are_rejected(): void
    {
        $response = $this->get('/image?path=security-test-temp.txt');

        // This should be rejected with 403 because it's not an image or video
        $response->assertStatus(403);
        $response->assertSee('Access restricted to image and video files only.');
    }
}
