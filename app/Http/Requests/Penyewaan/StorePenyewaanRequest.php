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
            'penyewaan_sttspembayaran' => 'sometimes|in:Lunas,Belum Dibayar,DP',
            'penyewaan_sttskembali'    => 'sometimes|in:Sudah Kembali,Belum Kembali',
            'penyewaan_totalharga'     => 'required|integer|min:0',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data penyewaan!';
    }
}