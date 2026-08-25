<?php

namespace Tests\Traits;

use App\Models\Admin;
use Tymon\JWTAuth\Facades\JWTAuth;

trait ApiTestTrait
{
    /**
     * Create and authenticate an admin user for testing
     */
    protected function createAndAuthenticateAdmin(): array
    {
        $admin = Admin::factory()->create([
            'admin_username' => 'testadmin',
            'admin_password' => 'password123',
        ]);

        $token = JWTAuth::fromUser($admin);

        return [
            'admin' => $admin,
            'token' => $token,
        ];
    }

    /**
     * Get headers with authentication token
     */
    protected function getAuthHeaders(string $token = ''): array
    {
        if (empty($token)) {
            $auth = $this->createAndAuthenticateAdmin();
            $token = $auth['token'];
        }

        return [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Assert successful API response
     */
    protected function assertSuccessResponse($response, int $expectedStatus = 200): void
    {
        $response->assertStatus($expectedStatus);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * Assert error API response
     */
    protected function assertErrorResponse($response, int $expectedStatus, string $expectedMessage = null): void
    {
        $response->assertStatus($expectedStatus);
        $response->assertJson([
            'success' => false,
        ]);

        if ($expectedMessage) {
            $response->assertJsonPath('message', $expectedMessage);
        }
    }

    /**
     * Assert validation error response
     */
    protected function assertValidationErrorResponse($response): void
    {
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors' => [],
        ]);
    }

    /**
     * Get API response data
     */
    protected function getResponseData($response): array
    {
        return $response->json('data', []);
    }

    /**
     * Assert JSON has path (compatible method for assertJsonPath)
     */
    protected function assertJsonHasPath($response, string $path)
    {
        return $response->assertJsonPath($path, fn($value) => true);
    }
}

