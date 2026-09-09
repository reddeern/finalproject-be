<?php

namespace App\Http\Requests\Penyewaan;

use App\Http\Requests\BaseApiRequest;

class StorePenyewaanRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'penyewaan_pelanggan_id'   => 'required|integer|exists:pelanggan,pelanggan_id',
            'penyewaan_tglsewa'        => 'required|date',
            'penyewaan_tglkembali'     => 'required|date|after_or_equal:penyewaan_tglsewa',
            'penyewaan_sttspembayaran' => 'sometimes|in:Lunas,Belum Dibayar',
            'penyewaan_sttskembali'    => 'sometimes|in:Sudah Kembali,Belum Kembali',
            'penyewaan_totalharga'     => 'required|integer|min:0',

            // Array detail alat yang disewa
            'detail'                   => 'required|array|min:1',
            'detail.*.alat_id'         => 'required|integer|exists:alat,alat_id',
            'detail.*.jumlah'          => 'required|integer|min:1',
            'detail.*.subharga'        => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'detail.required'         => 'Minimal harus ada 1 alat yang disewa',
            'detail.array'            => 'Detail harus berupa array',
            'detail.*.alat_id.required' => 'ID alat wajib diisi pada setiap detail',
            'detail.*.alat_id.exists' => 'Salah satu alat yang dipilih tidak ditemukan',
            'detail.*.jumlah.required'  => 'Jumlah wajib diisi pada setiap detail',
            'detail.*.jumlah.min'       => 'Jumlah minimal 1',
            'detail.*.subharga.required' => 'Subharga wajib diisi pada setiap detail',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data penyewaan!';
    }
}