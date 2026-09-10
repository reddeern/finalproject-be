<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Models\Alat;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $query = Alat::with('kategori');

            // Filter by kategori
            if ($request->filled('kategori_id')) {
                $query->where('alat_kategori_id', $request->kategori_id);
            }

            // Search by name or description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('alat_nama', 'like', "%{$search}%")
                      ->orWhere('alat_deskripsi', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'alat_id');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = ['alat_nama', 'alat_hargaperhari', 'alat_stok', 'alat_id'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            }

            // Pagination
            $perPage = (int) $request->get('per_page', 10);
            $perPage = min(max($perPage, 1), 50);
            $page = (int) $request->get('page', 1);

            $total = $query->count();
            $data = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully get alat data',
                'data' => $data,
                'meta' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
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
