<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pelanggan\StorePelangganRequest;
use App\Http\Requests\Pelanggan\UpdatePelangganRequest;
use App\Models\Pelanggan;
use App\Traits\ApiResponse;

class PelangganController extends Controller
{
    use ApiResponse;

    /**
     * Menampilkan semua pelanggan
     */
    public function index()
    {
        try {
            $data = Pelanggan::with('pelangganData')->get();

            // Tambahkan URL file identitas
            $data->transform(function ($pelanggan) {
                $pelanggan->pelangganData->transform(function ($item) {
                    if ($item->pelanggan_data_file) {
                        $item->pelanggan_data_file_url = asset(
                            'storage/' . $item->pelanggan_data_file
                        );
                    } else {
                        $item->pelanggan_data_file_url = null;
                    }

                    return $item;
                });

                return $pelanggan;
            });

            if ($data->isEmpty()) {
                return $this->successResponse(
                    null,
                    'Successfully get pelanggan data'
                );
            }

            return $this->successResponse(
                $data,
                'Successfully get pelanggan data'
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
     * Menampilkan detail pelanggan
     */
    public function show($id)
    {
        try {
            $pelanggan = Pelanggan::with('pelangganData')
                ->find($id);

            if (!$pelanggan) {
                return $this->errorResponse(
                    'Data pelanggan tidak ditemukan',
                    404
                );
            }

            // Tambahkan URL file identitas
            $pelanggan->pelangganData->transform(function ($item) {
                if ($item->pelanggan_data_file) {
                    $item->pelanggan_data_file_url = asset(
                        'storage/' . $item->pelanggan_data_file
                    );
                } else {
                    $item->pelanggan_data_file_url = null;
                }

                return $item;
            });

            return $this->successResponse(
                $pelanggan,
                'Successfully get pelanggan data'
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
     * Menambahkan pelanggan
     */
    public function store(StorePelangganRequest $request)
    {
        try {
            $pelanggan = Pelanggan::create(
                $request->validated()
            );

            return $this->successResponse(
                $pelanggan,
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
     * Memperbarui pelanggan
     */
    public function update(
        UpdatePelangganRequest $request,
        $id
    ) {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return $this->errorResponse(
                    'Data pelanggan tidak ditemukan',
                    404
                );
            }

            $pelanggan->update(
                $request->validated()
            );

            // Ambil data terbaru beserta identitas
            $pelanggan = Pelanggan::with('pelangganData')
                ->find($id);

            // Tambahkan URL file
            $pelanggan->pelangganData->transform(function ($item) {
                if ($item->pelanggan_data_file) {
                    $item->pelanggan_data_file_url = asset(
                        'storage/' . $item->pelanggan_data_file
                    );
                } else {
                    $item->pelanggan_data_file_url = null;
                }

                return $item;
            });

            return $this->successResponse(
                $pelanggan,
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
     * Menghapus pelanggan
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return $this->errorResponse(
                    'Data pelanggan tidak ditemukan',
                    404
                );
            }

            $pelanggan->delete();

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