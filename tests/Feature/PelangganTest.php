<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;

class PelangganTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = $this->createAndAuthenticateAdmin();
    }

    /**
     * Test get all customers successfully
     */
    public function test_get_all_customers_successfully()
    {
        Pelanggan::factory()->count(5)->create();

        $response = $this->getJson('/api/pelanggan', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'pelanggan_id',
                    'pelanggan_nama',
                    'pelanggan_alamat',
                    'pelanggan_notelp',
                    'pelanggan_email',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test get all customers when empty
     */
    public function test_get_all_customers_when_empty()
    {
        $response = $this->getJson('/api/pelanggan', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data', null);
    }

    /**
     * Test get customers without token
     */
    public function test_get_customers_without_token()
    {
        $response = $this->getJson('/api/pelanggan');

        $response->assertStatus(401);
    }

    /**
     * Test get single customer successfully
     */
    public function test_get_single_customer_successfully()
    {
        $pelanggan = Pelanggan::factory()->create([
            'pelanggan_nama' => 'John Doe',
            'pelanggan_email' => 'john@example.com',
        ]);

        $response = $this->getJson("/api/pelanggan/{$pelanggan->pelanggan_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'pelanggan_id',
                'pelanggan_nama',
                'pelanggan_alamat',
                'pelanggan_notelp',
                'pelanggan_email',
                'pelangganData',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('data.pelanggan_nama', 'John Doe');
    }

    /**
     * Test get non-existent customer
     */
    public function test_get_non_existent_customer()
    {
        $response = $this->getJson('/api/pelanggan/999', $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data pelanggan tidak ditemukan');
    }

    /**
     * Test create customer successfully
     */
    public function test_create_customer_successfully()
    {
        $data = [
            'pelanggan_nama' => 'Jane Smith',
            'pelanggan_alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'jane@example.com',
        ];

        $response = $this->postJson('/api/pelanggan', $data, $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('message', 'Berhasil menambahkan data pelanggan');
        $response->assertJsonPath('data.pelanggan_nama', 'Jane Smith');

        $this->assertDatabaseHas('pelanggan', [
            'pelanggan_nama' => 'Jane Smith',
            'pelanggan_email' => 'jane@example.com',
        ]);
    }

    /**
     * Test create customer with duplicate email
     */
    public function test_create_customer_with_duplicate_email()
    {
        Pelanggan::factory()->create([
            'pelanggan_email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_email');
    }

    /**
     * Test create customer with invalid email format
     */
    public function test_create_customer_with_invalid_email_format()
    {
        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'invalid-email',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_email');
    }

    /**
     * Test create customer with missing required fields
     */
    public function test_create_customer_with_missing_required_fields()
    {
        $response = $this->postJson('/api/pelanggan', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_nama');
        $this->assertJsonHasPath($response,'errors.pelanggan_alamat');
        $this->assertJsonHasPath($response,'errors.pelanggan_notelp');
        $this->assertJsonHasPath($response,'errors.pelanggan_email');
    }

    /**
     * Test create customer with empty fields
     */
    public function test_create_customer_with_empty_fields()
    {
        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => '',
            'pelanggan_alamat' => '',
            'pelanggan_notelp' => '',
            'pelanggan_email' => '',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create customer with name exceeding max length
     */
    public function test_create_customer_with_name_exceeding_max_length()
    {
        $longName = str_repeat('a', 151);

        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => $longName,
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create customer with address exceeding max length
     */
    public function test_create_customer_with_address_exceeding_max_length()
    {
        $longAddress = str_repeat('a', 201);

        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => $longAddress,
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create customer with phone exceeding max length
     */
    public function test_create_customer_with_phone_exceeding_max_length()
    {
        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '081234567890123456',
            'pelanggan_email' => 'john@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create customer without token
     */
    public function test_create_customer_without_token()
    {
        $response = $this->postJson('/api/pelanggan', [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'john@example.com',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test update customer successfully
     */
    public function test_update_customer_successfully()
    {
        $pelanggan = Pelanggan::factory()->create([
            'pelanggan_nama' => 'Old Name',
            'pelanggan_email' => 'old@example.com',
        ]);

        $response = $this->putJson("/api/pelanggan/{$pelanggan->pelanggan_id}", [
            'pelanggan_nama' => 'New Name',
            'pelanggan_alamat' => 'New Address',
            'pelanggan_notelp' => '08987654321',
            'pelanggan_email' => 'new@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil memperbarui data pelanggan');
        $response->assertJsonPath('data.pelanggan_nama', 'New Name');

        $this->assertDatabaseHas('pelanggan', [
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_nama' => 'New Name',
            'pelanggan_email' => 'new@example.com',
        ]);
    }

    /**
     * Test update customer with non-existent id
     */
    public function test_update_customer_with_non_existent_id()
    {
        $response = $this->putJson('/api/pelanggan/999', [
            'pelanggan_nama' => 'New Name',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'new@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data pelanggan tidak ditemukan');
    }

    /**
     * Test update customer with duplicate email
     */
    public function test_update_customer_with_duplicate_email()
    {
        $pelanggan1 = Pelanggan::factory()->create([
            'pelanggan_email' => 'john@example.com',
        ]);

        Pelanggan::factory()->create([
            'pelanggan_email' => 'jane@example.com',
        ]);

        $response = $this->putJson("/api/pelanggan/{$pelanggan1->pelanggan_id}", [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'jane@example.com',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test update customer with invalid email
     */
    public function test_update_customer_with_invalid_email()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->putJson("/api/pelanggan/{$pelanggan->pelanggan_id}", [
            'pelanggan_nama' => 'John Doe',
            'pelanggan_alamat' => 'Address',
            'pelanggan_notelp' => '08123456789',
            'pelanggan_email' => 'invalid-email',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test delete customer successfully
     */
    public function test_delete_customer_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->deleteJson("/api/pelanggan/{$pelanggan->pelanggan_id}", [], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil menghapus data pelanggan');

        $this->assertSoftDeleted('pelanggan', [
            'pelanggan_id' => $pelanggan->pelanggan_id,
        ]);
    }

    /**
     * Test delete non-existent customer
     */
    public function test_delete_non_existent_customer()
    {
        $response = $this->deleteJson('/api/pelanggan/999', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data pelanggan tidak ditemukan');
    }

    /**
     * Test delete customer without token
     */
    public function test_delete_customer_without_token()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->deleteJson("/api/pelanggan/{$pelanggan->pelanggan_id}");

        $response->assertStatus(401);
    }

    /**
     * Test customer includes pelanggan data relationship
     */
    public function test_customer_includes_pelanggan_data_relationship()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->getJson("/api/pelanggan/{$pelanggan->pelanggan_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'pelangganData',
            ],
        ]);
    }
}
