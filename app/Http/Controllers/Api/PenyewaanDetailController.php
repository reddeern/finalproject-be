<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PenyewaanDetail\StorePenyewaanDetailRequest;
use App\Http\Requests\PenyewaanDetail\UpdatePenyewaanDetailRequest;
use App\Models\PenyewaanDetail;
use App\Traits\ApiResponse;

class PenyewaanDetailController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            // If pelanggan: filter by own penyewaan only
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                $data = PenyewaanDetail::whereHas('penyewaan', function ($q) use ($customerId) {
                    $q->where('penyewaan_pelanggan_id', $customerId);
                })->with('alat')->get();
            } else {
                $data = PenyewaanDetail::with('alat')->get();
            }

            if ($data->isEmpty()) {
                return $this->successResponse(null, 'Successfully get penyewaan_detail data');
            }

            return $this->successResponse($data, 'Successfully get penyewaan_detail data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $item = PenyewaanDetail::with(['penyewaan', 'alat'])->find($id);

            if (! $item) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            // If pelanggan: check ownership
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($item->penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            return $this->successResponse($item, 'Successfully get penyewaan_detail data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StorePenyewaanDetailRequest $request)
    {
        try {
            // Validate ownership: pelanggan can only add to own penyewaan
            if (auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                $penyewaan = \App\Models\Penyewaan::find($request->penyewaan_detail_penyewaan_id);
                
                if (!$penyewaan || $penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            $item = PenyewaanDetail::create($request->validated());

            return $this->successResponse($item, 'Berhasil menambahkan data penyewaan_detail', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function update(UpdatePenyewaanDetailRequest $request, $id)
    {
        try {
            $item = PenyewaanDetail::find($id);

            if (! $item) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            // If pelanggan: check ownership
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($item->penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            $item->update($request->validated());

            return $this->successResponse($item, 'Berhasil memperbarui data penyewaan_detail');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = PenyewaanDetail::find($id);

            if (! $item) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            // If pelanggan: check ownership
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($item->penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            $item->delete();

            return $this->successResponse(null, 'Berhasil menghapus data penyewaan_detail');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }
}