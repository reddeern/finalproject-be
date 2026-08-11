<?php

namespace App\Http\Requests\Pelanggan;

use App\Http\Requests\BaseApiRequest;

class UpdatePelangganRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $id = $this->route('pelanggan');

        return [
            'pelanggan_nama'   => 'sometimes|required|string|max:150',
            'pelanggan_alamat' => 'sometimes|required|string|max:200',
            'pelanggan_notelp' => 'sometimes|required|string|max:13',
            'pelanggan_email'  => "sometimes|required|email|max:100|unique:pelanggan,pelanggan_email,{$id},pelanggan_id",
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data pelanggan!';
    }
}