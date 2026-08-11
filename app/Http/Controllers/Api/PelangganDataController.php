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
            $data = PelangganData::all();

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

            return $this->successResponse($item, 'Successfully get pelanggan_data data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StorePelangganDataRequest $request)
    {
        try {
            $path = $request->file('pelanggan_data_file')->store('pelanggan_data', 'public');

            $item = PelangganData::create([
                'pelanggan_data_pelanggan_id' => $request->pelanggan_data_pelanggan_id,
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