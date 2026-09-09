<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PelangganData\StorePelangganDataRequest;
use App\Http\Requests\PelangganData\UpdatePelangganDataRequest;
use App\Models\PelangganData;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class PelangganDataController extends Controller
{
    use ApiResponse;

    /**
     * GET /pelanggan-data
     */
    public function index()
    {
        try {
            // Jika login sebagai pelanggan,
            // hanya tampilkan data miliknya
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();

                $data = PelangganData::with('pelanggan')
                    ->where('pelanggan_data_pelanggan_id', $customerId)
                    ->get();
            } else {
                $data = PelangganData::with('pelanggan')->get();
            }

            // Tambahkan URL file untuk frontend
            $data->transform(function ($item) {
                if ($item->pelanggan_data_file) {
                    $item->pelanggan_data_file_url = asset(
                        'storage/' . $item->pelanggan_data_file
                    );
                } else {
                    $item->pelanggan_data_file_url = null;
                }

                return $item;
            });

            if ($data->isEmpty()) {
                return $this->successResponse(
                    null,
                    'Successfully get pelanggan_data data'
                );
            }

            return $this->successResponse(
                $data,
                'Successfully get pelanggan_data data'
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
     * GET /pelanggan-data/{id}
     */
    public function show($id)
    {
        try {
            $item = PelangganData::with('pelanggan')->find($id);

            if (!$item) {
                return $this->errorResponse(
                    'Data tidak ditemukan',
                    404
                );
            }

            // Jika pelanggan, cek kepemilikan data
            if (
                auth()->check() &&
                auth()->guard() === 'pelanggan-api'
            ) {
                $customerId = auth('pelanggan-api')->id();

                if (
                    $item->pelanggan_data_pelanggan_id != $customerId
                ) {
                    return $this->errorResponse(
                        'Unauthorized',
                        403
                    );
                }
            }

            // Tambahkan URL file
            if ($item->pelanggan_data_file) {
                $item->pelanggan_data_file_url = asset(
                    'storage/' . $item->pelanggan_data_file
                );
            } else {
                $item->pelanggan_data_file_url = null;
            }

            return $this->successResponse(
                $item,
                'Successfully get pelanggan_data data'
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
     * POST /pelanggan-data
     */
    public function store(StorePelangganDataRequest $request)
    {
        try {
            // Tentukan ID pelanggan
            $customerId = null;

            if (auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
            } else {
                $customerId = $request->pelanggan_data_pelanggan_id;
            }

            // Pastikan file ada
            if (!$request->hasFile('pelanggan_data_file')) {
                return $this->errorResponse(
                    'File pelanggan tidak masuk ke backend',
                    422
                );
            }

            // Simpan file
            $path = $request->file('pelanggan_data_file')
                ->store('pelanggan_data', 'public');

            // Simpan data
            $item = PelangganData::create([
                'pelanggan_data_pelanggan_id' => $customerId,
                'pelanggan_data_jenis' => $request->pelanggan_data_jenis,
                'pelanggan_data_file' => $path,
            ]);

            // URL untuk frontend
            $item->pelanggan_data_file_url = asset(
                'storage/' . $item->pelanggan_data_file
            );

            return $this->successResponse(
                $item,
                'Berhasil menambahkan data pelanggan',
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
     * POST /pelanggan-data/{id}
     *
     * Gunakan POST + _method=PUT dari React
     * supaya upload file terbaca Laravel.
     */
    public function update(
        UpdatePelangganDataRequest $request,
        $id
    ) {
        try {
            // Cari data
            $item = PelangganData::find($id);

            if (!$item) {
                return $this->errorResponse(
                    'Data tidak ditemukan',
                    404
                );
            }

            // Jika pelanggan, cek kepemilikan
            if (
                auth()->check() &&
                auth()->guard() === 'pelanggan-api'
            ) {
                $customerId = auth('pelanggan-api')->id();

                if (
                    $item->pelanggan_data_pelanggan_id != $customerId
                ) {
                    return $this->errorResponse(
                        'Unauthorized',
                        403
                    );
                }
            }

            // Ambil data selain file
            $payload = $request->safe()->except(
                'pelanggan_data_file'
            );

            // =====================================================
            // HANDLE FILE
            // =====================================================

            if ($request->hasFile('pelanggan_data_file')) {

                // Hapus file lama
                if ($item->pelanggan_data_file) {
                    Storage::disk('public')->delete(
                        $item->pelanggan_data_file
                    );
                }

                // Simpan file baru
                $path = $request->file('pelanggan_data_file')
                    ->store('pelanggan_data', 'public');

                // Masukkan file baru ke database
                $payload['pelanggan_data_file'] = $path;
            }

            // =====================================================
            // UPDATE DATABASE
            // =====================================================

            $item->update($payload);

            // Ambil data terbaru
            $item->refresh();

            // URL file untuk frontend
            if ($item->pelanggan_data_file) {
                $item->pelanggan_data_file_url = asset(
                    'storage/' . $item->pelanggan_data_file
                );
            } else {
                $item->pelanggan_data_file_url = null;
            }

            return $this->successResponse(
                $item,
                'Berhasil memperbarui data pelanggan'
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
     * DELETE /pelanggan-data/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari data
            $item = PelangganData::find($id);

            if (!$item) {
                return $this->errorResponse(
                    'Data tidak ditemukan',
                    404
                );
            }

            // Jika pelanggan, cek kepemilikan
            if (
                auth()->check() &&
                auth()->guard() === 'pelanggan-api'
            ) {
                $customerId = auth('pelanggan-api')->id();

                if (
                    $item->pelanggan_data_pelanggan_id != $customerId
                ) {
                    return $this->errorResponse(
                        'Unauthorized',
                        403
                    );
                }
            }

            // Hapus file
            if ($item->pelanggan_data_file) {
                Storage::disk('public')->delete(
                    $item->pelanggan_data_file
                );
            }

            // Hapus database
            $item->delete();

            return $this->successResponse(
                null,
                'Berhasil menghapus data pelanggan'
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