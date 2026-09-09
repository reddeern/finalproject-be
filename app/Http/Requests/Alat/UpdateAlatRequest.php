<?php

namespace App\Http\Requests\Alat;

use App\Http\Requests\BaseApiRequest;

class UpdateAlatRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'alat_kategori_id'   => 'sometimes|required|integer|exists:kategori,kategori_id',
            'alat_nama'          => 'sometimes|required|string|max:150',
            'alat_deskripsi'     => 'sometimes|required|string|max:255',
            'alat_hargaperhari'  => 'sometimes|required|integer|min:0',
            'alat_stok'          => 'sometimes|required|integer|min:0',
            'alat_gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data alat!';
    }
}