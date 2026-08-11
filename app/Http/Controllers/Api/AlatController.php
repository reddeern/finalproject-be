<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Models\Alat;
use App\Traits\ApiResponse;

class AlatController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $data = Alat::with('kategori')->get();

            if ($data->isEmpty()) {
                return $this->successResponse(null, 'Successfully get alat data');
            }

            return $this->successResponse($data, 'Successfully get alat data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $alat = Alat::with('kategori')->find($id);

            if (! $alat) {
                return $this->errorResponse('Data alat tidak ditemukan', 404);
            }

            return $this->successResponse($alat, 'Successfully get alat data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StoreAlatRequest $request)
    {
        try {
            $alat = Alat::create($request->validated());

            return $this->successResponse($alat, 'Berhasil menambahkan data alat', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function update(UpdateAlatRequest $request, $id)
    {
        try {
            $alat = Alat::find($id);

            if (! $alat) {
                return $this->errorResponse('Data alat tidak ditemukan', 404);
            }

            $alat->update($request->validated());

            return $this->successResponse($alat, 'Berhasil memperbarui data alat');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $alat = Alat::find($id);

            if (! $alat) {
                return $this->errorResponse('Data alat tidak ditemukan', 404);
            }

            $alat->delete();

            return $this->successResponse(null, 'Berhasil menghapus data alat');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }
}