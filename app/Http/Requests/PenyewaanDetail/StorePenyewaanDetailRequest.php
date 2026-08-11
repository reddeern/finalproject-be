<?php

namespace App\Http\Requests\PenyewaanDetail;

use App\Http\Requests\BaseApiRequest;

class StorePenyewaanDetailRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'penyewaan_detail_penyewaan_id' => 'required|integer|exists:penyewaan,penyewaan_id',
            'penyewaan_detail_alat_id'      => 'required|integer|exists:alat,alat_id',
            'penyewaan_detail_jumlah'       => 'required|integer|min:1',
            'penyewaan_detail_subharga'     => 'required|integer|min:0',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data penyewaan_detail!';
    }
}