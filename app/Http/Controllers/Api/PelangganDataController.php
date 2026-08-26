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

    public function index()
    {
        try {
            // If pelanggan: filter by own ID
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                $data = PelangganData::where('pelanggan_data_pelanggan_id', $customerId)->get();
            } else {
                $data = PelangganData::all();
            }

            if ($data->isEmpty()) {
                return $this->successResponse(null, 'Successfully get pelanggan_data data');
            }

            return $this->successResponse($data, 'Successfully get pelanggan_data data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $item = PelangganData::with('pelanggan')->find($id);

            if (! $item) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            // If pelanggan: check ownership
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($item->pelanggan_data_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            return $this->successResponse($item, 'Successfully get pelanggan_data data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StorePelangganDataRequest $request)
    {
        try {
            // If pelanggan: auto-set own ID
            $customerId = null;
            if (auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
            } else {
                $customerId = $request->pelanggan_data_pelanggan_id;
            }

            $path = $request->file('pelanggan_data_file')->store('pelanggan_data', 'public');

            $item = PelangganData::create([
                'pelanggan_data_pelanggan_id' => $customerId,
                'pelanggan_data_jenis'        => $request->pelanggan_data_jenis,
                'pelanggan_data_file'         => $path,
            ]);

            return $this->successResponse($item, 'Berhasil menambahkan data pelanggan', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function update(UpdatePelangganDataRequest $request, $id)
    {
        try {
            $item = PelangganData::find($id);

            if (! $item) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            // If pelanggan: check ownership
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($item->pelanggan_data_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            $payload = $request->safe()->except('pelanggan_data_file');

            if ($request->hasFile('pelanggan_data_file')) {
                if ($item->pelanggan_data_file) {
                    Storage::disk('public')->delete($item->pelanggan_data_file);
                }
                $payload['pelanggan_data_file'] = $request->file('pelanggan_data_file')->store('pelanggan_data', 'public');
            }

            $item->update($payload);

            return $this->successResponse($item, 'Berhasil memperbarui data pelanggan');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = PelangganData::find($id);

            if (! $item) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            // If pelanggan: check ownership
            if (auth()->check() && auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($item->pelanggan_data_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            if ($item->pelanggan_data_file) {
                Storage::disk('public')->delete($item->pelanggan_data_file);
            }

            $item->delete();

            return $this->successResponse(null, 'Berhasil menghapus data pelanggan');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }
}