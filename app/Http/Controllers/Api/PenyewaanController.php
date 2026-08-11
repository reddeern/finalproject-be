<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penyewaan\StorePenyewaanRequest;
use App\Http\Requests\Penyewaan\UpdatePenyewaanRequest;
use App\Models\Penyewaan;
use App\Traits\ApiResponse;

class PenyewaanController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $data = Penyewaan::with('pelanggan')->get();

            if ($data->isEmpty()) {
                return $this->successResponse(null, 'Successfully get penyewaan data');
            }

            return $this->successResponse($data, 'Successfully get penyewaan data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $penyewaan = Penyewaan::with(['pelanggan', 'detail.alat'])->find($id);

            if (! $penyewaan) {
                return $this->errorResponse('Data penyewaan tidak ditemukan', 404);
            }

            return $this->successResponse($penyewaan, 'Successfully get penyewaan data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StorePenyewaanRequest $request)
    {
        try {
            $penyewaan = Penyewaan::create($request->validated());

            return $this->successResponse($penyewaan, 'Berhasil menambahkan data penyewaan', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function update(UpdatePenyewaanRequest $request, $id)
    {
        try {
            $penyewaan = Penyewaan::find($id);

            if (! $penyewaan) {
                return $this->errorResponse('Data penyewaan tidak ditemukan', 404);
            }

            $penyewaan->update($request->validated());

            return $this->successResponse($penyewaan, 'Berhasil memperbarui data penyewaan');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $penyewaan = Penyewaan::find($id);

            if (! $penyewaan) {
                return $this->errorResponse('Data penyewaan tidak ditemukan', 404);
            }

            $penyewaan->delete();

            return $this->successResponse(null, 'Berhasil menghapus data penyewaan');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }
}