<?php

namespace App\Http\Requests\Penyewaan;

use App\Http\Requests\BaseApiRequest;

class UpdatePenyewaanRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'penyewaan_pelanggan_id'   => 'sometimes|required|integer|exists:pelanggan,pelanggan_id',
            'penyewaan_tglsewa'        => 'sometimes|required|date',
            'penyewaan_tglkembali'     => 'sometimes|required|date|after_or_equal:penyewaan_tglsewa',
            'penyewaan_sttspembayaran' => 'sometimes|in:Lunas,Belum Dibayar,DP',
            'penyewaan_sttskembali'    => 'sometimes|in:Sudah Kembali,Belum Kembali',
            'penyewaan_totalharga'     => 'sometimes|required|integer|min:0',

            // Array detail bersifat opsional saat update
            // (kalau dikirim, berarti mau replace semua detail lama)
            'detail'                   => 'sometimes|array|min:1',
            'detail.*.alat_id'         => 'required_with:detail|integer|exists:alat,alat_id',
            'detail.*.jumlah'          => 'required_with:detail|integer|min:1',
            'detail.*.subharga'        => 'required_with:detail|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'detail.array'             => 'Detail harus berupa array',
            'detail.*.alat_id.exists'  => 'Salah satu alat yang dipilih tidak ditemukan',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data penyewaan!';
    }
}