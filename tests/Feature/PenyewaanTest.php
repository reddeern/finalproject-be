<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Penyewaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;

class PenyewaanTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = $this->createAndAuthenticateAdmin();
    }

    /**
     * Test get all rentals successfully
     */
    public function test_get_all_rentals_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        Penyewaan::factory()->count(5)->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->getJson('/api/penyewaan', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'penyewaan_id',
                    'penyewaan_pelanggan_id',
                    'penyewaan_tglsewa',
                    'penyewaan_tglkembali',
                    'penyewaan_sttspembayaran',
                    'penyewaan_sttskembali',
                    'penyewaan_totalharga',
                    'pelanggan',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test get all rentals when empty
     */
    public function test_get_all_rentals_when_empty()
    {
        $response = $this->getJson('/api/penyewaan', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data', null);
    }

    /**
     * Test get rentals without token
     */
    public function test_get_rentals_without_token()
    {
        $response = $this->getJson('/api/penyewaan');

        $response->assertStatus(401);
    }

    /**
     * Test get single rental successfully
     */
    public function test_get_single_rental_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->getJson("/api/penyewaan/{$penyewaan->penyewaan_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'penyewaan_id',
                'penyewaan_pelanggan_id',
                'penyewaan_tglsewa',
                'penyewaan_tglkembali',
                'penyewaan_sttspembayaran',
                'penyewaan_sttskembali',
                'penyewaan_totalharga',
                'pelanggan',
                'detail',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test get non-existent rental
     */
    public function test_get_non_existent_rental()
    {
        $response = $this->getJson('/api/penyewaan/999', $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data penyewaan tidak ditemukan');
    }

    /**
     * Test create rental successfully
     */
    public function test_create_rental_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();

        $data = [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_sttspembayaran' => 'Belum Dibayar',
            'penyewaan_sttskembali' => 'Belum Kembali',
            'penyewaan_totalharga' => 2000000,
        ];

        $response = $this->postJson('/api/penyewaan', $data, $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('message', 'Berhasil menambahkan data penyewaan');
        $response->assertJsonPath('data.penyewaan_totalharga', 2000000);

        $this->assertDatabaseHas('penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_totalharga' => 2000000,
        ]);
    }

    /**
     * Test create rental with non-existent pelanggan
     */
    public function test_create_rental_with_non_existent_pelanggan()
    {
        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => 999,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_pelanggan_id');
    }

    /**
     * Test create rental with return date before rental date
     */
    public function test_create_rental_with_return_date_before_rental_date()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-05',
            'penyewaan_tglkembali' => '2026-09-01',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_tglkembali');
    }

    /**
     * Test create rental with missing required fields
     */
    public function test_create_rental_with_missing_required_fields()
    {
        $response = $this->postJson('/api/penyewaan', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response,'errors.penyewaan_pelanggan_id');
        $this->assertJsonHasPath($response,'errors.penyewaan_tglsewa');
        $this->assertJsonHasPath($response,'errors.penyewaan_tglkembali');
        $this->assertJsonHasPath($response,'errors.penyewaan_totalharga');
    }

    /**
     * Test create rental with invalid date format
     */
    public function test_create_rental_with_invalid_date_format()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => 'invalid-date',
            'penyewaan_tglkembali' => 'invalid-date',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create rental with negative total price
     */
    public function test_create_rental_with_negative_total_price()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => -100,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create rental with invalid payment status
     */
    public function test_create_rental_with_invalid_payment_status()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_sttspembayaran' => 'INVALID',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create rental with invalid return status
     */
    public function test_create_rental_with_invalid_return_status()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_sttskembali' => 'INVALID',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test create rental without token
     */
    public function test_create_rental_without_token()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => 2000000,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test update rental successfully
     */
    public function test_update_rental_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_sttspembayaran' => 'Belum Dibayar',
            'penyewaan_totalharga' => 1000000,
        ]);

        $response = $this->putJson("/api/penyewaan/{$penyewaan->penyewaan_id}", [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-10',
            'penyewaan_sttspembayaran' => 'Lunas',
            'penyewaan_sttskembali' => 'Sudah Kembali',
            'penyewaan_totalharga' => 2500000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil memperbarui data penyewaan');
        $response->assertJsonPath('data.penyewaan_sttspembayaran', 'Lunas');
        $response->assertJsonPath('data.penyewaan_totalharga', 2500000);

        $this->assertDatabaseHas('penyewaan', [
            'penyewaan_id' => $penyewaan->penyewaan_id,
            'penyewaan_sttspembayaran' => 'Lunas',
            'penyewaan_totalharga' => 2500000,
        ]);
    }

    /**
     * Test update rental with non-existent id
     */
    public function test_update_rental_with_non_existent_id()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->putJson('/api/penyewaan/999', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data penyewaan tidak ditemukan');
    }

    /**
     * Test update rental with invalid pelanggan
     */
    public function test_update_rental_with_invalid_pelanggan()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->putJson("/api/penyewaan/{$penyewaan->penyewaan_id}", [
            'penyewaan_pelanggan_id' => 999,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test delete rental successfully
     */
    public function test_delete_rental_successfully()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->deleteJson("/api/penyewaan/{$penyewaan->penyewaan_id}", [], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil menghapus data penyewaan');

        $this->assertSoftDeleted('penyewaan', [
            'penyewaan_id' => $penyewaan->penyewaan_id,
        ]);
    }

    /**
     * Test delete non-existent rental
     */
    public function test_delete_non_existent_rental()
    {
        $response = $this->deleteJson('/api/penyewaan/999', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data penyewaan tidak ditemukan');
    }

    /**
     * Test delete rental without token
     */
    public function test_delete_rental_without_token()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penyewaan = Penyewaan::factory()->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->deleteJson("/api/penyewaan/{$penyewaan->penyewaan_id}");

        $response->assertStatus(401);
    }

    /**
     * Test rental default payment status
     */
    public function test_rental_default_payment_status()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('data.penyewaan_sttspembayaran', 'Belum Dibayar');
    }

    /**
     * Test rental default return status
     */
    public function test_rental_default_return_status()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->postJson('/api/penyewaan', [
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            'penyewaan_tglsewa' => '2026-09-01',
            'penyewaan_tglkembali' => '2026-09-05',
            'penyewaan_totalharga' => 2000000,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonPath('data.penyewaan_sttskembali', 'Belum Kembali');
    }

    /**
     * Test rental includes pelanggan relationship
     */
    public function test_rental_includes_pelanggan_relationship()
    {
        $pelanggan = Pelanggan::factory()->create([
            'pelanggan_nama' => 'John Doe',
        ]);

        $penyewaan = Penyewaan::factory()->create([
            'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
        ]);

        $response = $this->getJson("/api/penyewaan/{$penyewaan->penyewaan_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data.pelanggan.pelanggan_nama', 'John Doe');
    }
}
