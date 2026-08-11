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
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data penyewaan!';
    }
}