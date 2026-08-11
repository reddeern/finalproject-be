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
            $data = PenyewaanDetail::with('alat')->get();

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

            return $this->successResponse($item, 'Successfully get penyewaan_detail data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StorePenyewaanDetailRequest $request)
    {
        try {
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

            $item->delete();

            return $this->successResponse(null, 'Berhasil menghapus data penyewaan_detail');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }
}