<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\PelangganData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;

class PelangganDataTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = $this->createAndAuthenticateAdmin();
        Storage::fake('public');
    }

    /**
     * Test get all customer data successfully
     */
    public function test_get_all_customer_data_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        PelangganData::factory()->count(5)->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->getJson('/api/pelanggan-data', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'pelanggan_data_id',
                    'pelanggan_data_pelanggan_id',
                    'pelanggan_data_jenis',
                    'pelanggan_data_file',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test get all customer data when empty
     */
    public function test_get_all_customer_data_when_empty()
    {
        $response = $this->getJson('/api/pelanggan-data', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data', null);
    }

    /**
     * Test get customer data without token
     */
    public function test_get_customer_data_without_token()
    {
        $response = $this->getJson('/api/pelanggan-data');

        $response->assertStatus(401);
    }

    /**
     * Test get single customer data successfully
     */
    public function test_get_single_customer_data_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
        ]);

        $response = $this->getJson("/api/pelanggan-data/{$data->pelanggan_data_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'pelanggan_data_id',
                'pelanggan_data_pelanggan_id',
                'pelanggan_data_jenis',
                'pelanggan_data_file',
                'pelanggan',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('data.pelanggan_data_jenis', 'KTP');
    }

    /**
     * Test get non-existent customer data
     */
    public function test_get_non_existent_customer_data()
    {
        $response = $this->getJson('/api/pelanggan-data/999', $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data tidak ditemukan');
    }

    /**
     * Test create customer data with file upload successfully
     */
    public function test_create_customer_data_with_file_upload_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $file = UploadedFile::fake()->image('ktp.jpg', 200, 200);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $file,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('message', 'Berhasil menambahkan data pelanggan');
        $response->assertJsonPath('data.pelanggan_data_jenis', 'KTP');

        $this->assertDatabaseHas('pelanggan_data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
        ]);

        Storage::disk('public')->assertExists($response->json('data.pelanggan_data_file'));
    }

    /**
     * Test create customer data with SIM type
     */
    public function test_create_customer_data_with_sim_type()
    {
        $pelanggan = Pelanggan::factory()->create();
        $file = UploadedFile::fake()->image('sim.png', 200, 200);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'SIM',
            'pelanggan_data_file' => $file,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('data.pelanggan_data_jenis', 'SIM');
    }

    /**
     * Test create customer data with non-existent pelanggan
     */
    public function test_create_customer_data_with_non_existent_pelanggan()
    {
        $file = UploadedFile::fake()->image('ktp.jpg', 200, 200);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => 999,
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $file,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_data_pelanggan_id');
    }

    /**
     * Test create customer data with missing file
     */
    public function test_create_customer_data_with_missing_file()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_data_file');
    }

    /**
     * Test create customer data with invalid file type
     */
    public function test_create_customer_data_with_invalid_file_type()
    {
        $pelanggan = Pelanggan::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $file,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_data_file');
    }

    /**
     * Test create customer data with file exceeding max size
     */
    public function test_create_customer_data_with_file_exceeding_max_size()
    {
        $pelanggan = Pelanggan::factory()->create();
        $file = UploadedFile::fake()->image('large.jpg')->size(3000);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $file,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_data_file');
    }

    /**
     * Test create customer data with invalid jenis
     */
    public function test_create_customer_data_with_invalid_jenis()
    {
        $pelanggan = Pelanggan::factory()->create();
        $file = UploadedFile::fake()->image('doc.jpg', 200, 200);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'INVALID',
            'pelanggan_data_file' => $file,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.pelanggan_data_jenis');
    }

    /**
     * Test create customer data with missing required fields
     */
    public function test_create_customer_data_with_missing_required_fields()
    {
        $response = $this->postJson('/api/pelanggan-data', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.pelanggan_data_pelanggan_id');
        $this->assertJsonHasPath($response,'errors.pelanggan_data_jenis');
        $this->assertJsonHasPath($response,'errors.pelanggan_data_file');
    }

    /**
     * Test create customer data without token
     */
    public function test_create_customer_data_without_token()
    {
        $pelanggan = Pelanggan::factory()->create();
        $file = UploadedFile::fake()->image('ktp.jpg', 200, 200);

        $response = $this->postJson('/api/pelanggan-data', [
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $file,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test update customer data successfully
     */
    public function test_update_customer_data_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            'pelanggan_data_jenis' => 'KTP',
        ]);

        $response = $this->putJson("/api/pelanggan-data/{$data->pelanggan_data_id}", [
            'pelanggan_data_jenis' => 'SIM',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil memperbarui data pelanggan');
        $response->assertJsonPath('data.pelanggan_data_jenis', 'SIM');

        $this->assertDatabaseHas('pelanggan_data', [
            'pelanggan_data_id' => $data->pelanggan_data_id,
            'pelanggan_data_jenis' => 'SIM',
        ]);
    }

    /**
     * Test update customer data with file replacement
     */
    public function test_update_customer_data_with_file_replacement()
    {
        $pelanggan = Pelanggan::factory()->create();
        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $oldFile = $data->pelanggan_data_file;
        $newFile = UploadedFile::fake()->image('new_ktp.jpg', 200, 200);

        $response = $this->putJson("/api/pelanggan-data/{$data->pelanggan_data_id}", [
            'pelanggan_data_jenis' => 'KTP',
            'pelanggan_data_file' => $newFile,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $newFilePath = $response->json('data.pelanggan_data_file');
        $this->assertNotEquals($oldFile, $newFilePath);
        Storage::disk('public')->assertExists($newFilePath);
    }

    /**
     * Test update customer data with non-existent id
     */
    public function test_update_customer_data_with_non_existent_id()
    {
        $response = $this->putJson('/api/pelanggan-data/999', [
            'pelanggan_data_jenis' => 'KTP',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data tidak ditemukan');
    }

    /**
     * Test update customer data with invalid jenis
     */
    public function test_update_customer_data_with_invalid_jenis()
    {
        $pelanggan = Pelanggan::factory()->create();
        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->putJson("/api/pelanggan-data/{$data->pelanggan_data_id}", [
            'pelanggan_data_jenis' => 'INVALID',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test delete customer data successfully
     */
    public function test_delete_customer_data_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $filePath = $data->pelanggan_data_file;

        $response = $this->deleteJson("/api/pelanggan-data/{$data->pelanggan_data_id}", [], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil menghapus data pelanggan');

        $this->assertSoftDeleted('pelanggan_data', [
            'pelanggan_data_id' => $data->pelanggan_data_id,
        ]);

        Storage::disk('public')->assertMissing($filePath);
    }

    /**
     * Test delete non-existent customer data
     */
    public function test_delete_non_existent_customer_data()
    {
        $response = $this->deleteJson('/api/pelanggan-data/999', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data tidak ditemukan');
    }

    /**
     * Test delete customer data without token
     */
    public function test_delete_customer_data_without_token()
    {
        $pelanggan = Pelanggan::factory()->create();
        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->deleteJson("/api/pelanggan-data/{$data->pelanggan_data_id}");

        $response->assertStatus(401);
    }

    /**
     * Test customer data includes pelanggan relationship
     */
    public function test_customer_data_includes_pelanggan_relationship()
    {
        $pelanggan = Pelanggan::factory()->create([
            'pelanggan_nama' => 'John Doe',
        ]);

        $data = PelangganData::factory()->create([
            'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->getJson("/api/pelanggan-data/{$data->pelanggan_data_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data.pelanggan.pelanggan_nama', 'John Doe');
    }
}
