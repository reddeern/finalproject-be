<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    /**
     * Test successful login with correct credentials
     */
    public function test_login_with_correct_credentials()
    {
        Admin::factory()->create([
            'admin_username' => 'testadmin',
            'admin_password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'admin_username' => 'testadmin',
            'password' => 'password123',
        ]);

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'admin' => [
                    'admin_id',
                    'admin_username',
                ],
            ],
        ]);
        $response->assertJsonPath('data.token_type', 'bearer');
        $response->assertJsonPath('message', 'Login berhasil');
    }

    /**
     * Test login with incorrect username
     */
    public function test_login_with_incorrect_username()
    {
        Admin::factory()->create([
            'admin_username' => 'testadmin',
            'admin_password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'admin_username' => 'wrongadmin',
            'password' => 'password123',
        ]);

        $this->assertErrorResponse($response, 401, 'Username atau password salah');
    }

    /**
     * Test login with incorrect password
     */
    public function test_login_with_incorrect_password()
    {
        Admin::factory()->create([
            'admin_username' => 'testadmin',
            'admin_password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'admin_username' => 'testadmin',
            'password' => 'wrongpassword',
        ]);

        $this->assertErrorResponse($response, 401, 'Username atau password salah');
    }

    /**
     * Test login with missing username
     */
    public function test_login_with_missing_username()
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $this->assertValidationErrorResponse($response);
        $response->assertJsonPath('errors.admin_username', ['The admin username field is required.']);
    }

    /**
     * Test login with missing password
     */
    public function test_login_with_missing_password()
    {
        $response = $this->postJson('/api/login', [
            'admin_username' => 'testadmin',
        ]);

        $this->assertValidationErrorResponse($response);
        $response->assertJsonPath('errors.password', ['The password field is required.']);
    }

    /**
     * Test login with empty credentials
     */
    public function test_login_with_empty_credentials()
    {
        $response = $this->postJson('/api/login', [
            'admin_username' => '',
            'password' => '',
        ]);

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.admin_username');
        $this->assertJsonHasPath($response, 'errors.password');
    }

    /**
     * Test get authenticated admin profile (me endpoint)
     */
    public function test_get_authenticated_admin_profile()
    {
        $auth = $this->createAndAuthenticateAdmin();

        $response = $this->getJson('/api/me', $this->getAuthHeaders($auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'admin_id',
                'admin_username',
            ],
        ]);
        $response->assertJsonPath('data.admin_username', 'testadmin');
    }

    /**
     * Test get profile without token
     */
    public function test_get_profile_without_token()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

    /**
     * Test get profile with invalid token
     */
    public function test_get_profile_with_invalid_token()
    {
        $response = $this->getJson('/api/me', [
            'Authorization' => 'Bearer invalid_token_here',
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

    /**
     * Test logout successfully
     */
    public function test_logout_successfully()
    {
        $auth = $this->createAndAuthenticateAdmin();

        $response = $this->postJson('/api/logout', [], $this->getAuthHeaders($auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil logout');
        $response->assertJsonPath('data', null);
    }

    /**
     * Test logout without token
     */
    public function test_logout_without_token()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

    /**
     * Test refresh token successfully
     */
    public function test_refresh_token_successfully()
    {
        $auth = $this->createAndAuthenticateAdmin();

        $response = $this->postJson('/api/refresh', [], $this->getAuthHeaders($auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'admin',
            ],
        ]);
        $response->assertJsonPath('message', 'Token berhasil diperbarui');
    }

    /**
     * Test refresh token with invalid token
     */
    public function test_refresh_token_with_invalid_token()
    {
        $response = $this->postJson('/api/refresh', [], [
            'Authorization' => 'Bearer invalid_token',
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

    /**
     * Test refresh token without token
     */
    public function test_refresh_token_without_token()
    {
        $response = $this->postJson('/api/refresh');

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

    /**
     * Test login returns token with correct TTL
     */
    public function test_login_token_has_correct_ttl()
    {
        Admin::factory()->create([
            'admin_username' => 'testadmin',
            'admin_password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'admin_username' => 'testadmin',
            'password' => 'password123',
        ]);

        $this->assertSuccessResponse($response, 200);
        $expiresIn = $response->json('data.expires_in');
        $this->assertIsInt($expiresIn);
        $this->assertGreaterThan(0, $expiresIn);
    }
}
