<?php

namespace App\Http\Requests\PenyewaanDetail;

use App\Http\Requests\BaseApiRequest;

class UpdatePenyewaanDetailRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'penyewaan_detail_penyewaan_id' => 'sometimes|required|integer|exists:penyewaan,penyewaan_id',
            'penyewaan_detail_alat_id'      => 'sometimes|required|integer|exists:alat,alat_id',
            'penyewaan_detail_jumlah'       => 'sometimes|required|integer|min:1',
            'penyewaan_detail_subharga'     => 'sometimes|required|integer|min:0',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data penyewaan_detail!';
    }
}