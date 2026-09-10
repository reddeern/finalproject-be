<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Models\Alat;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    use ApiResponse;

    /**
     * GET /alat
     * Menampilkan semua data alat
     */
    public function index()
    {
        try {
            $query = Alat::with('kategori');

            if ($data->isEmpty()) {
                return $this->successResponse(
                    null,
                    'Successfully get alat data'
                );
            }

            return $this->successResponse(
                $data,
                'Successfully get alat data'
            );

        } catch (\Throwable $e) {
            return $this->errorResponse(
                'There error in Internal Server',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * GET /alat/{id}
     * Menampilkan satu data alat
     */
    public function show($id)
    {
        try {
            $alat = Alat::with('kategori')->find($id);

            if (!$alat) {
                return $this->errorResponse(
                    'Data alat tidak ditemukan',
                    404
                );
            }

            return $this->successResponse(
                $alat,
                'Successfully get alat data'
            );

        } catch (\Throwable $e) {
            return $this->errorResponse(
                'There error in Internal Server',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * POST /alat
     * Menambahkan data alat
     */
    public function store(StoreAlatRequest $request)
    {
        try {

            // Cek apakah gambar masuk
            if (!$request->hasFile('alat_gambar')) {
                return $this->errorResponse(
                    'File gambar tidak masuk ke backend',
                    422
                );
            }

            // Ambil data yang sudah divalidasi
            $validatedData = $request->validated();

            // Simpan gambar
            // Sama seperti yang sudah berhasil di store
            $path = $request->file('alat_gambar')->store(
                'alat',
                'public'
            );

            // Simpan path gambar ke database
            $validatedData['alat_gambar'] = $path;

            // Simpan data alat
            $alat = Alat::create($validatedData);

            return $this->successResponse(
                $alat,
                'Berhasil menambahkan data alat',
                201
            );

        } catch (\Throwable $e) {
            return $this->errorResponse(
                'There error in Internal Server',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * PUT/PATCH /alat/{id}
     * Memperbarui data alat
     */
    public function update(UpdateAlatRequest $request, $id)
    {
        try {

            // Cari data alat berdasarkan ID
            $alat = Alat::find($id);

            if (!$alat) {
                return $this->errorResponse(
                    'Data alat tidak ditemukan',
                    404
                );
            }

            // Ambil data hasil validasi
            $validatedData = $request->validated();

            /*
            |--------------------------------------------------------------------------
            | HANDLE GAMBAR
            |--------------------------------------------------------------------------
            */

            // Jika ada gambar baru yang dikirim
            if ($request->hasFile('alat_gambar')) {

                // Hapus gambar lama
                if ($alat->alat_gambar) {
                    Storage::disk('public')->delete(
                        $alat->alat_gambar
                    );
                }

                // Simpan gambar baru
                // SAMA seperti store()
                $path = $request->file('alat_gambar')->store(
                    'alat',
                    'public'
                );

                // Masukkan path gambar baru
                $validatedData['alat_gambar'] = $path;
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE DATABASE
            |--------------------------------------------------------------------------
            */

            $alat->update($validatedData);

            // Ambil data terbaru dari database
            $alat = Alat::find($id);

            return $this->successResponse(
                $alat,
                'Berhasil memperbarui data alat'
            );

        } catch (\Throwable $e) {
            return $this->errorResponse(
                'There error in Internal Server',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * DELETE /alat/{id}
     * Menghapus data alat
     */
    public function destroy($id)
    {
        try {

            // Cari data alat
            $alat = Alat::find($id);

            if (!$alat) {
                return $this->errorResponse(
                    'Data alat tidak ditemukan',
                    404
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS GAMBAR
            |--------------------------------------------------------------------------
            */

            if ($alat->alat_gambar) {
                Storage::disk('public')->delete(
                    $alat->alat_gambar
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS DATA
            |--------------------------------------------------------------------------
            */

            $alat->delete();

            return $this->successResponse(
                null,
                'Berhasil menghapus data alat'
            );

        } catch (\Throwable $e) {
            return $this->errorResponse(
                'There error in Internal Server',
                500,
                $e->getMessage()
            );
        }
    }
}
