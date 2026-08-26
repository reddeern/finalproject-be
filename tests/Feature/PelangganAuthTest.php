<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\PelangganData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PelangganAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test register pelanggan successfully
     */
    public function test_register_pelanggan_successfully()
    {
        $response = $this->postJson('/api/pelanggan/register', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Jl. Merdeka No. 123',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertEquals(200, $response->status());
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'pelanggan' => [
                    'pelanggan_id',
                    'pelanggan_nama',
                    'pelanggan_email',
                ],
            ],
        ]);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Registrasi berhasil');

        $this->assertDatabaseHas('pelanggan', [
            'pelanggan_email' => 'john@example.com',
            'pelanggan_nama' => 'John Doe',
        ]);
    }

    /**
     * Test register with file upload
     */
    public function test_register_with_file_upload()
    {
        $file = UploadedFile::fake()->image('ktp.jpg', 200, 200);

        $response = $this->postJson('/api/pelanggan/register', [
            'pelanggan_nama' => 'Jane Smith',
            'pelanggan_alamat' => 'Jl. Sudirman No. 456',
            'pelanggan_notelp' => '08987654321',
            'pelanggan_email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $file,
        ]);

        $this->assertEquals(200, $response->status());
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('pelanggan', [
            'pelanggan_email' => 'jane@example.com',
        ]);

        $this->assertDatabaseHas('pelanggan_data', [
            'pelanggan_data_jenis' => 'KTP',
        ]);
    }

    /**
     * Test register with duplicate email
     */
    public function test_register_with_duplicate_email()
    {
        Pelanggan::factory()->create([
            'pelanggan_email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/pelanggan/register', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Jl. Merdeka No. 123',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertEquals(422, $response->status());
        $response->assertJsonPath('success', false);
    }

    /**
     * Test register with missing required fields
     */
    public function test_register_with_missing_required_fields()
    {
        $response = $this->postJson('/api/pelanggan/register', [
            'pelanggan_nama' => 'John Doe',
        ]);

        $this->assertEquals(422, $response->status());
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors' => [],
        ]);
    }

    /**
     * Test login pelanggan successfully
     */
    public function test_login_pelanggan_successfully()
    {
        // Register first
        $registerResponse = $this->postJson('/api/pelanggan/register', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Jl. Merdeka No. 123',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Now login
        $response = $this->postJson('/api/pelanggan/login', [
            'pelanggan_email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $this->assertEquals(200, $response->status());
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'pelanggan',
            ],
        ]);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Login berhasil');
    }

    /**
     * Test login with invalid credentials
     */
    public function test_login_with_invalid_credentials()
    {
        Pelanggan::factory()->create([
            'pelanggan_email' => 'john@example.com',
            'pelanggan_password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/pelanggan/login', [
            'pelanggan_email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertEquals(401, $response->status());
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Email atau password salah');
    }

    /**
     * Test login with non-existent email
     */
    public function test_login_with_non_existent_email()
    {
        $response = $this->postJson('/api/pelanggan/login', [
            'pelanggan_email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $this->assertEquals(401, $response->status());
        $response->assertJsonPath('success', false);
    }

    /**
     * Test login with missing fields
     */
    public function test_login_with_missing_fields()
    {
        $response = $this->postJson('/api/pelanggan/login', [
            'pelanggan_email' => 'john@example.com',
        ]);

        $this->assertEquals(422, $response->status());
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
    }

    /**
     * Test get profile without token
     */
    public function test_get_profile_without_token()
    {
        $response = $this->getJson('/api/pelanggan/me');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test get profile with invalid token
     */
    public function test_get_profile_with_invalid_token()
    {
        $response = $this->getJson('/api/pelanggan/me', [
            'Authorization' => 'Bearer invalid_token',
            'Accept' => 'application/json',
        ]);

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test logout pelanggan
     */
    public function test_logout_pelanggan()
    {
        // Register pelanggan
        $registerResponse = $this->postJson('/api/pelanggan/register', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Jl. Merdeka No. 123',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $token = $registerResponse->json('data.access_token');

        // Logout - use withHeaders properly
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->post('/api/pelanggan/logout');

        $this->assertEquals(200, $response->status());
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Berhasil logout');
    }

    /**
     * Test logout without token
     */
    public function test_logout_without_token()
    {
        $response = $this->postJson('/api/pelanggan/logout');

        $this->assertEquals(401, $response->status());
    }
}
