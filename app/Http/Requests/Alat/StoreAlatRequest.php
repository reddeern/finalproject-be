<?php

namespace App\Http\Requests\Alat;

use App\Http\Requests\BaseApiRequest;

class StoreAlatRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'alat_kategori_id'   => 'required|integer|exists:kategori,kategori_id',
            'alat_nama'          => 'required|string|max:150',
            'alat_deskripsi'     => 'required|string|max:255',
            'alat_hargaperhari'  => 'required|integer|min:0',
            'alat_stok'          => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'alat_kategori_id.exists' => 'Kategori yang dipilih tidak ditemukan',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data alat!';
    }
}