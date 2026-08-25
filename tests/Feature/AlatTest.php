<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;

class AlatTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = $this->createAndAuthenticateAdmin();
    }

    /**
     * Test get all equipment successfully
     */
    public function test_get_all_equipment_successfully()
    {
        $kategori = Kategori::factory()->create();
        Alat::factory()->count(5)->create([
            'alat_kategori_id' => $kategori->kategori_id,
        ]);

        $response = $this->getJson('/api/alat', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'alat_id',
                    'alat_kategori_id',
                    'alat_nama',
                    'alat_deskripsi',
                    'alat_hargaperhari',
                    'alat_stok',
                    'kategori',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test get all equipment when empty
     */
    public function test_get_all_equipment_when_empty()
    {
        $response = $this->getJson('/api/alat', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data', null);
    }

    /**
     * Test get equipment without token
     */
    public function test_get_equipment_without_token()
    {
        $response = $this->getJson('/api/alat');

        $response->assertStatus(401);
    }

    /**
     * Test get single equipment successfully
     */
    public function test_get_single_equipment_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Laptop Dell',
        ]);

        $response = $this->getJson("/api/alat/{$alat->alat_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'alat_id',
                'alat_kategori_id',
                'alat_nama',
                'alat_deskripsi',
                'alat_hargaperhari',
                'alat_stok',
                'kategori',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('data.alat_nama', 'Laptop Dell');
    }

    /**
     * Test get non-existent equipment
     */
    public function test_get_non_existent_equipment()
    {
        $response = $this->getJson('/api/alat/999', $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data alat tidak ditemukan');
    }

    /**
     * Test create equipment successfully
     */
    public function test_create_equipment_successfully()
    {
        $kategori = Kategori::factory()->create();

        $data = [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Proyektor',
            'alat_deskripsi' => 'Proyektor 4K dengan brightness 5000 lumens',
            'alat_hargaperhari' => 500000,
            'alat_stok' => 10,
        ];

        $response = $this->postJson('/api/alat', $data, $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('message', 'Berhasil menambahkan data alat');
        $response->assertJsonPath('data.alat_nama', 'Proyektor');

        $this->assertDatabaseHas('alat', [
            'alat_nama' => 'Proyektor',
            'alat_hargaperhari' => 500000,
        ]);
    }

    /**
     * Test create equipment with non-existent category
     */
    public function test_create_equipment_with_non_existent_category()
    {
        $response = $this->postJson('/api/alat', [
            'alat_kategori_id' => 999,
            'alat_nama' => 'Proyektor',
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => 500000,
            'alat_stok' => 10,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.alat_kategori_id');
    }

    /**
     * Test create equipment with missing required fields
     */
    public function test_create_equipment_with_missing_required_fields()
    {
        $response = $this->postJson('/api/alat', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.alat_kategori_id');
        $this->assertJsonHasPath($response, 'errors.alat_nama');
        $this->assertJsonHasPath($response, 'errors.alat_deskripsi');
    }

    /**
     * Test create equipment with negative price
     */
    public function test_create_equipment_with_negative_price()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->postJson('/api/alat', [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Proyektor',
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => -100,
            'alat_stok' => 10,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create equipment with negative stock
     */
    public function test_create_equipment_with_negative_stock()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->postJson('/api/alat', [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Proyektor',
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => 500000,
            'alat_stok' => -5,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create equipment with name exceeding max length
     */
    public function test_create_equipment_with_name_exceeding_max_length()
    {
        $kategori = Kategori::factory()->create();
        $longName = str_repeat('a', 151);

        $response = $this->postJson('/api/alat', [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => $longName,
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => 500000,
            'alat_stok' => 10,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create equipment without token
     */
    public function test_create_equipment_without_token()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->postJson('/api/alat', [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Test',
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => 100,
            'alat_stok' => 1,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test update equipment successfully
     */
    public function test_update_equipment_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Old Name',
            'alat_stok' => 5,
        ]);

        $response = $this->putJson("/api/alat/{$alat->alat_id}", [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'New Name',
            'alat_deskripsi' => 'Updated description',
            'alat_hargaperhari' => 600000,
            'alat_stok' => 15,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil memperbarui data alat');
        $response->assertJsonPath('data.alat_nama', 'New Name');
        $response->assertJsonPath('data.alat_stok', 15);

        $this->assertDatabaseHas('alat', [
            'alat_id' => $alat->alat_id,
            'alat_nama' => 'New Name',
            'alat_stok' => 15,
        ]);
    }

    /**
     * Test update equipment with non-existent id
     */
    public function test_update_equipment_with_non_existent_id()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->putJson('/api/alat/999', [
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'New Name',
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => 100,
            'alat_stok' => 1,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data alat tidak ditemukan');
    }

    /**
     * Test update equipment with invalid category
     */
    public function test_update_equipment_with_invalid_category()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
        ]);

        $response = $this->putJson("/api/alat/{$alat->alat_id}", [
            'alat_kategori_id' => 999,
            'alat_nama' => 'New Name',
            'alat_deskripsi' => 'Test',
            'alat_hargaperhari' => 100,
            'alat_stok' => 1,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test delete equipment successfully
     */
    public function test_delete_equipment_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
        ]);

        $response = $this->deleteJson("/api/alat/{$alat->alat_id}", [], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil menghapus data alat');

        $this->assertSoftDeleted('alat', [
            'alat_id' => $alat->alat_id,
        ]);
    }

    /**
     * Test delete non-existent equipment
     */
    public function test_delete_non_existent_equipment()
    {
        $response = $this->deleteJson('/api/alat/999', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data alat tidak ditemukan');
    }

    /**
     * Test delete equipment without token
     */
    public function test_delete_equipment_without_token()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
        ]);

        $response = $this->deleteJson("/api/alat/{$alat->alat_id}");

        $response->assertStatus(401);
    }

    /**
     * Test equipment includes category relationship
     */
    public function test_equipment_includes_category_relationship()
    {
        $kategori = Kategori::factory()->create([
            'kategori_nama' => 'Elektronik',
        ]);

        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
        ]);

        $response = $this->getJson("/api/alat/{$alat->alat_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data.kategori.kategori_nama', 'Elektronik');
    }
}
