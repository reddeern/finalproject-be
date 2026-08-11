<?php

namespace App\Http\Requests\Kategori;

use App\Http\Requests\BaseApiRequest;

class StoreKategoriRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'kategori_nama' => 'required|string|max:100|unique:kategori,kategori_nama',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data kategori!';
    }
}