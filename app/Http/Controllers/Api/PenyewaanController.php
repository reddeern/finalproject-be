<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penyewaan\StorePenyewaanRequest;
use App\Http\Requests\Penyewaan\UpdatePenyewaanRequest;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use App\Models\Alat;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenyewaanController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            if (Auth::check() && Auth::user()->role === 'pelanggan-api') {
                $customerId = Auth::user()->id;
                $data = Penyewaan::where('penyewaan_pelanggan_id', $customerId)
                    ->with(['pelanggan', 'detail.alat']) 
                    ->get();
            } else {
                $data = Penyewaan::with(['pelanggan', 'detail.alat'])->get();
            }

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

            if (Auth::check() && Auth::user()->role === 'pelanggan-api') {
                $customerId = Auth::user()->id;
                if ($penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            return $this->successResponse($penyewaan, 'Successfully get penyewaan data');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    public function store(StorePenyewaanRequest $request)
    {
        try {
            $penyewaan = null;
            
            DB::transaction(function () use ($request, &$penyewaan) {
                $data = $request->validated();
                if (auth()->guard() === 'pelanggan-api') {
                    $data['penyewaan_pelanggan_id'] = auth('pelanggan-api')->id();
                }

                $penyewaan = Penyewaan::create($request->safe()->except('detail'));

                if ($request->has('detail')) {
                    foreach ($request->detail as $item) {
                        PenyewaanDetail::create([
                            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
                            'penyewaan_detail_alat_id'      => $item['alat_id'],
                            'penyewaan_detail_jumlah'       => $item['jumlah'],
                            'penyewaan_detail_subharga'     => $item['subharga'],
                        ]);
                    }
                }
            });

            $penyewaan->load('detail.alat');
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

            if (auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            DB::transaction(function () use ($request, $penyewaan) {
                $penyewaan->update($request->safe()->except('detail'));

                if ($request->has('detail')) {
                    $penyewaan->detail()->delete();

                    foreach ($request->detail as $item) {
                        PenyewaanDetail::create([
                            'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
                            'penyewaan_detail_alat_id'      => $item['alat_id'],
                            'penyewaan_detail_jumlah'       => $item['jumlah'],
                            'penyewaan_detail_subharga'     => $item['subharga'],
                        ]);
                    }
                }
            });

            $penyewaan->load('detail.alat');
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

            if (auth()->guard() === 'pelanggan-api') {
                $customerId = auth('pelanggan-api')->id();
                if ($penyewaan->penyewaan_pelanggan_id !== $customerId) {
                    return $this->errorResponse('Unauthorized', 403);
                }
            }

            $penyewaan->delete();
            return $this->successResponse(null, 'Berhasil menghapus data penyewaan');
        } catch (\Throwable $e) {
            return $this->errorResponse('There error in Internal Server', 500, $e->getMessage());
        }
    }

    // FUNGSI BARU UNTUK KEMBALIKAN BARANG DAN TAMBAH STOK
    public function kembali($id)
    {
        try {
            $penyewaan = Penyewaan::with('detail')->find($id);

            if (! $penyewaan) {
                return $this->errorResponse('Data penyewaan tidak ditemukan', 404);
            }

            // Mencegah double klik kembalikan
            if ($penyewaan->penyewaan_sttskembali === 'Sudah Kembali') {
                return $this->errorResponse('Transaksi ini sudah dikembalikan sebelumnya', 400);
            }

            DB::transaction(function () use ($penyewaan) {
                // 1. Ubah Status
                $penyewaan->update([
                    'penyewaan_sttskembali' => 'Sudah Kembali',
                    'penyewaan_sttspembayaran' => 'Lunas'
                ]);

                // 2. Kembalikan Stok Alat
                foreach ($penyewaan->detail as $item) {
                    $alat = Alat::find($item->penyewaan_detail_alat_id);
                    if ($alat) {
                        $alat->alat_stok += $item->penyewaan_detail_jumlah;
                        $alat->save();
                    }
                }
            });

            return $this->successResponse(null, 'Transaksi berhasil dikembalikan, status Lunas, dan stok bertambah.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan saat memproses pengembalian', 500, $e->getMessage());
        }
    }
}