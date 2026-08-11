<?php

namespace App\Http\Requests\PelangganData;

use App\Http\Requests\BaseApiRequest;

class StorePelangganDataRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pelanggan_data_pelanggan_id' => 'required|integer|exists:pelanggan,pelanggan_id',
            'pelanggan_data_jenis'        => 'required|in:KTP,SIM',
            'pelanggan_data_file'         => 'required|file|mimes:jpg,png,jpeg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_data_file.mimes'    => 'File harus memiliki format .jpg, .png, atau .jpeg',
            'pelanggan_data_file.max'      => 'Ukuran file maksimal 2MB',
            'pelanggan_data_jenis.in'      => 'Jenis data hanya boleh KTP atau SIM',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal menambahkan data pelanggan!';
    }
}