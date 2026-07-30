<?php

namespace Tests\Feature;

use Tests\TestCase;

class StoragePathTraversalTest extends TestCase
{
    private string $testFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testFilePath = storage_path('app/public/security-test-temp.txt');
        file_put_contents($this->testFilePath, 'Hello security test');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }

        parent::tearDown();
    }

    /**
     * Test that legitimate files can be retrieved via /storage/{path}.
     */
    public function test_legitimate_files_can_be_retrieved(): void
    {
        $response = $this->get('/storage/security-test-temp.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * Test that directory traversal attempts are blocked.
     */
    public function test_directory_traversal_attempts_are_blocked(): void
    {
        $response = $this->get('/storage/../../.env');
        // This should be rejected with 403
        $response->assertStatus(403);
    }

    /**
     * Test that URL encoded directory traversal attempts are blocked.
     */
    public function test_encoded_directory_traversal_attempts_are_blocked(): void
    {
        $response = $this->get('/storage/..%2F..%2F.env');
        $response->assertStatus(403);
    }

    /**
     * Test that development routes are restricted in non-local environments.
     */
    public function test_development_routes_are_blocked_in_production(): void
    {
        // Set environment to production
        $this->app['env'] = 'production';

        $this->get('/storage-link')->assertStatus(403);
        $this->get('/run-migrations')->assertStatus(403);
        $this->get('/test-upload')->assertStatus(403);
    }

    /**
     * Test that development routes are accessible in local environment.
     */
    public function test_development_routes_are_accessible_in_local(): void
    {
        // Set environment to local
        $this->app['env'] = 'local';

        // /storage-link should not throw 403
        $response1 = $this->get('/storage-link');
        $this->assertNotEquals(403, $response1->getStatusCode());

        // /test-upload should not throw 403
        $response2 = $this->get('/test-upload');
        $this->assertNotEquals(403, $response2->getStatusCode());
    }
}
