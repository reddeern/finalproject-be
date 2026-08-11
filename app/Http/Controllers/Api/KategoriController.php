<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kategori\StoreKategoriRequest;
use App\Http\Requests\Kategori\UpdateKategoriRequest;
use App\Models\Kategori;
use App\Traits\ApiResponse;

class KategoriController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $data = Kategori::all();

            if ($data->isEmpty()) {
                return $this->successResponse(null, 'Successfully get kategori data');
            }

            return $this->successResponse($data, 'Successfully get kategori data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $kategori = Kategori::find($id);

            if (! $kategori) {
                return $this->errorResponse('Data kategori tidak ditemukan', 404);
            }

            return $this->successResponse($kategori, 'Successfully get kategori data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StoreKategoriRequest $request)
    {
        try {
            $kategori = Kategori::create($request->validated());

            return $this->successResponse($kategori, 'Berhasil menambahkan data kategori', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function update(UpdateKategoriRequest $request, $id)
    {
        try {
            $kategori = Kategori::find($id);

            if (! $kategori) {
                return $this->errorResponse('Data kategori tidak ditemukan', 404);
            }

            $kategori->update($request->validated());

            return $this->successResponse($kategori, 'Berhasil memperbarui data kategori');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = Kategori::find($id);

            if (! $kategori) {
                return $this->errorResponse('Data kategori tidak ditemukan', 404);
            }

            $kategori->delete();

            return $this->successResponse(null, 'Berhasil menghapus data kategori');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }
}