<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;

class PenyewaanDetailTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = $this->createAndAuthenticateAdmin();
    }

    /**
     * Test get all rental details successfully
     */
    public function test_get_all_rental_details_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        PenyewaanDetail::factory()->count(5)->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
        ]);

        $response = $this->getJson('/api/penyewaan-detail', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'penyewaan_detail_id',
                    'penyewaan_detail_penyewaan_id',
                    'penyewaan_detail_alat_id',
                    'penyewaan_detail_jumlah',
                    'penyewaan_detail_subharga',
                    'alat',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test get all rental details when empty
     */
    public function test_get_all_rental_details_when_empty()
    {
        $response = $this->getJson('/api/penyewaan-detail', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data', null);
    }

    /**
     * Test get rental details without token
     */
    public function test_get_rental_details_without_token()
    {
        $response = $this->getJson('/api/penyewaan-detail');

        $response->assertStatus(401);
    }

    /**
     * Test get single rental detail successfully
     */
    public function test_get_single_rental_detail_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $detail = PenyewaanDetail::factory()->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 3,
        ]);

        $response = $this->getJson("/api/penyewaan-detail/{$detail->penyewaan_detail_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'penyewaan_detail_id',
                'penyewaan_detail_penyewaan_id',
                'penyewaan_detail_alat_id',
                'penyewaan_detail_jumlah',
                'penyewaan_detail_subharga',
                'penyewaan',
                'alat',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('data.penyewaan_detail_jumlah', 3);
    }

    /**
     * Test get non-existent rental detail
     */
    public function test_get_non_existent_rental_detail()
    {
        $response = $this->getJson('/api/penyewaan-detail/999', $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data tidak ditemukan');
    }

    /**
     * Test create rental detail successfully
     */
    public function test_create_rental_detail_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $data = [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => 2500000,
        ];

        $response = $this->postJson('/api/penyewaan-detail', $data, $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('message', 'Berhasil menambahkan data penyewaan_detail');
        $response->assertJsonPath('data.penyewaan_detail_jumlah', 5);
        $response->assertJsonPath('data.penyewaan_detail_subharga', 2500000);

        $this->assertDatabaseHas('penyewaan_detail', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
        ]);
    }

    /**
     * Test create rental detail with non-existent penyewaan
     */
    public function test_create_rental_detail_with_non_existent_penyewaan()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);

        $response = $this->postJson('/api/penyewaan-detail', [
            'penyewaan_detail_penyewaan_id' => 999,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => 2500000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_penyewaan_id');
    }

    /**
     * Test create rental detail with non-existent alat
     */
    public function test_create_rental_detail_with_non_existent_alat()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $response = $this->postJson('/api/penyewaan-detail', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => 999,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => 2500000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_alat_id');
    }

    /**
     * Test create rental detail with zero quantity
     */
    public function test_create_rental_detail_with_zero_quantity()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $response = $this->postJson('/api/penyewaan-detail', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 0,
            'penyewaan_detail_subharga' => 0,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_jumlah');
    }

    /**
     * Test create rental detail with negative quantity
     */
    public function test_create_rental_detail_with_negative_quantity()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $response = $this->postJson('/api/penyewaan-detail', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => -5,
            'penyewaan_detail_subharga' => -2500000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create rental detail with negative price
     */
    public function test_create_rental_detail_with_negative_price()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $response = $this->postJson('/api/penyewaan-detail', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => -100,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create rental detail with missing required fields
     */
    public function test_create_rental_detail_with_missing_required_fields()
    {
        $response = $this->postJson('/api/penyewaan-detail', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_penyewaan_id');
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_alat_id');
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_jumlah');
        $this->assertJsonHasPath($response,'errors.penyewaan_detail_subharga');
    }

    /**
     * Test create rental detail without token
     */
    public function test_create_rental_detail_without_token()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $response = $this->postJson('/api/penyewaan-detail', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => 2500000,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test update rental detail successfully
     */
    public function test_update_rental_detail_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $detail = PenyewaanDetail::factory()->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 3,
        ]);

        $response = $this->putJson("/api/penyewaan-detail/{$detail->penyewaan_detail_id}", [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 10,
            'penyewaan_detail_subharga' => 5000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil memperbarui data penyewaan_detail');
        $response->assertJsonPath('data.penyewaan_detail_jumlah', 10);

        $this->assertDatabaseHas('penyewaan_detail', [
            'penyewaan_detail_id' => $detail->penyewaan_detail_id,
            'penyewaan_detail_jumlah' => 10,
        ]);
    }

    /**
     * Test update rental detail with non-existent id
     */
    public function test_update_rental_detail_with_non_existent_id()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $response = $this->putJson('/api/penyewaan-detail/999', [
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => 2500000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data tidak ditemukan');
    }

    /**
     * Test update rental detail with invalid penyewaan
     */
    public function test_update_rental_detail_with_invalid_penyewaan()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $detail = PenyewaanDetail::factory()->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
        ]);

        $response = $this->putJson("/api/penyewaan-detail/{$detail->penyewaan_detail_id}", [
            'penyewaan_detail_penyewaan_id' => 999,
            'penyewaan_detail_alat_id' => $alat->alat_id,
            'penyewaan_detail_jumlah' => 5,
            'penyewaan_detail_subharga' => 2500000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test delete rental detail successfully
     */
    public function test_delete_rental_detail_successfully()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $detail = PenyewaanDetail::factory()->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
        ]);

        $response = $this->deleteJson("/api/penyewaan-detail/{$detail->penyewaan_detail_id}", [], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil menghapus data penyewaan_detail');

        $this->assertSoftDeleted('penyewaan_detail', [
            'penyewaan_detail_id' => $detail->penyewaan_detail_id,
        ]);
    }

    /**
     * Test delete non-existent rental detail
     */
    public function test_delete_non_existent_rental_detail()
    {
        $response = $this->deleteJson('/api/penyewaan-detail/999', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data tidak ditemukan');
    }

    /**
     * Test delete rental detail without token
     */
    public function test_delete_rental_detail_without_token()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create(['alat_kategori_id' => $kategori->kategori_id]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $detail = PenyewaanDetail::factory()->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
        ]);

        $response = $this->deleteJson("/api/penyewaan-detail/{$detail->penyewaan_detail_id}");

        $response->assertStatus(401);
    }

    /**
     * Test rental detail includes penyewaan and alat relationships
     */
    public function test_rental_detail_includes_penyewaan_and_alat_relationships()
    {
        $kategori = Kategori::factory()->create();
        $alat = Alat::factory()->create([
            'alat_kategori_id' => $kategori->kategori_id,
            'alat_nama' => 'Laptop',
        ]);
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create(['penyewaan_pelanggan_id' => $pelanggan->pelanggan_id]);

        $detail = PenyewaanDetail::factory()->create([
            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_detail_alat_id' => $alat->alat_id,
        ]);

        $response = $this->getJson("/api/penyewaan-detail/{$detail->penyewaan_detail_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data.alat.alat_nama', 'Laptop');
        $response->assertJsonPath('data.penyewaan.penyewaan_id', $penyewaan->penyewaan_id);
    }
}
