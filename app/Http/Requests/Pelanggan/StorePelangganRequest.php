<?php

namespace App\Http\Requests\Pelanggan;

use App\Http\Requests\BaseApiRequest;

class StorePelangganRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pelanggan_nama'   => 'required|string|max:150',
            'pelanggan_alamat' => 'required|string|max:200',
            'pelanggan_notelp' => 'required|string|max:13',
            'pelanggan_email'  => 'required|email|max:100|unique:pelanggan,pelanggan_email',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data pelanggan!';
    }
}