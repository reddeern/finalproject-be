<?php

namespace App\Http\Requests\Kategori;

use App\Http\Requests\BaseApiRequest;

class UpdateKategoriRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $id = $this->route('kategori');

        return [
            'kategori_nama' => "sometimes|required|string|max:100|unique:kategori,kategori_nama,{$id},kategori_id",
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data kategori!';
    }
}