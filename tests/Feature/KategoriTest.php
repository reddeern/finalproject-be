<?php

namespace Tests\Feature;

use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ApiTestTrait;

class KategoriTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    /**
     * Setup for each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = $this->createAndAuthenticateAdmin();
    }

    /**
     * Test get all categories successfully
     */
    public function test_get_all_categories_successfully()
    {
        Kategori::factory()->count(5)->create();

        $response = $this->getJson('/api/kategori', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'kategori_id',
                    'kategori_nama',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test get all categories when empty
     */
    public function test_get_all_categories_when_empty()
    {
        $response = $this->getJson('/api/kategori', $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('data', null);
        $response->assertJsonPath('message', 'Successfully get kategori data');
    }

    /**
     * Test get categories without token
     */
    public function test_get_categories_without_token()
    {
        $response = $this->getJson('/api/kategori');

        $response->assertStatus(401);
    }

    /**
     * Test get single category successfully
     */
    public function test_get_single_category_successfully()
    {
        $kategori = Kategori::factory()->create([
            'kategori_nama' => 'Elektronik',
        ]);

        $response = $this->getJson("/api/kategori/{$kategori->kategori_id}", $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'kategori_id',
                'kategori_nama',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('data.kategori_nama', 'Elektronik');
    }

    /**
     * Test get non-existent category
     */
    public function test_get_non_existent_category()
    {
        $response = $this->getJson('/api/kategori/999', $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data kategori tidak ditemukan');
    }

    /**
     * Test create category successfully
     */
    public function test_create_category_successfully()
    {
        $data = [
            'kategori_nama' => 'Peralatan Olahraga',
        ];

        $response = $this->postJson('/api/kategori', $data, $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'kategori_id',
                'kategori_nama',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('message', 'Berhasil menambahkan data kategori');
        $response->assertJsonPath('data.kategori_nama', 'Peralatan Olahraga');

        $this->assertDatabaseHas('kategori', [
            'kategori_nama' => 'Peralatan Olahraga',
        ]);
    }

    /**
     * Test create category with duplicate name
     */
    public function test_create_category_with_duplicate_name()
    {
        Kategori::factory()->create([
            'kategori_nama' => 'Elektronik',
        ]);

        $response = $this->postJson('/api/kategori', [
            'kategori_nama' => 'Elektronik',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.kategori_nama');
    }

    /**
     * Test create category with missing name
     */
    public function test_create_category_with_missing_name()
    {
        $response = $this->postJson('/api/kategori', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.kategori_nama');
    }

    /**
     * Test create category with empty name
     */
    public function test_create_category_with_empty_name()
    {
        $response = $this->postJson('/api/kategori', [
            'kategori_nama' => '',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.kategori_nama');
    }

    /**
     * Test create category with name exceeding max length
     */
    public function test_create_category_with_name_exceeding_max_length()
    {
        $longName = str_repeat('a', 101);

        $response = $this->postJson('/api/kategori', [
            'kategori_nama' => $longName,
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.kategori_nama');
    }

    /**
     * Test create category without token
     */
    public function test_create_category_without_token()
    {
        $response = $this->postJson('/api/kategori', [
            'kategori_nama' => 'Test',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test update category successfully
     */
    public function test_update_category_successfully()
    {
        $kategori = Kategori::factory()->create([
            'kategori_nama' => 'Old Name',
        ]);

        $response = $this->putJson("/api/kategori/{$kategori->kategori_id}", [
            'kategori_nama' => 'New Name',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil memperbarui data kategori');
        $response->assertJsonPath('data.kategori_nama', 'New Name');

        $this->assertDatabaseHas('kategori', [
            'kategori_id' => $kategori->kategori_id,
            'kategori_nama' => 'New Name',
        ]);
    }

    /**
     * Test update category with non-existent id
     */
    public function test_update_category_with_non_existent_id()
    {
        $response = $this->putJson('/api/kategori/999', [
            'kategori_nama' => 'New Name',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data kategori tidak ditemukan');
    }

    /**
     * Test update category with duplicate name
     */
    public function test_update_category_with_duplicate_name()
    {
        $kategori1 = Kategori::factory()->create([
            'kategori_nama' => 'Kategori 1',
        ]);

        Kategori::factory()->create([
            'kategori_nama' => 'Kategori 2',
        ]);

        $response = $this->putJson("/api/kategori/{$kategori1->kategori_id}", [
            'kategori_nama' => 'Kategori 2',
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
        $this->assertJsonHasPath($response, 'errors.kategori_nama');
    }

    /**
     * Test update category with invalid data
     */
    public function test_update_category_with_invalid_data()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->putJson("/api/kategori/{$kategori->kategori_id}", [
            'kategori_nama' => str_repeat('a', 101),
        ], $this->getAuthHeaders($this->auth['token']));

        $this->assertValidationErrorResponse($response);
    }

    /**
     * Test delete category successfully
     */
    public function test_delete_category_successfully()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->deleteJson("/api/kategori/{$kategori->kategori_id}", [], $this->getAuthHeaders($this->auth['token']));

        $this->assertSuccessResponse($response, 200);
        $response->assertJsonPath('message', 'Berhasil menghapus data kategori');
        $response->assertJsonPath('data', null);

        $this->assertSoftDeleted('kategori', [
            'kategori_id' => $kategori->kategori_id,
        ]);
    }

    /**
     * Test delete non-existent category
     */
    public function test_delete_non_existent_category()
    {
        $response = $this->deleteJson('/api/kategori/999', [], $this->getAuthHeaders($this->auth['token']));

        $this->assertErrorResponse($response, 404, 'Data kategori tidak ditemukan');
    }

    /**
     * Test delete category without token
     */
    public function test_delete_category_without_token()
    {
        $kategori = Kategori::factory()->create();

        $response = $this->deleteJson("/api/kategori/{$kategori->kategori_id}");

        $response->assertStatus(401);
    }
}
