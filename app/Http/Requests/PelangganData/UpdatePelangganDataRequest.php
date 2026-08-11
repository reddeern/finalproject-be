<?php

namespace App\Http\Requests\PelangganData;

use App\Http\Requests\BaseApiRequest;

class UpdatePelangganDataRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pelanggan_data_pelanggan_id' => 'sometimes|required|integer|exists:pelanggan,pelanggan_id',
            'pelanggan_data_jenis'        => 'sometimes|required|in:KTP,SIM',
            'pelanggan_data_file'         => 'sometimes|file|mimes:jpg,png,jpeg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_data_file.mimes' => 'File harus memiliki format .jpg, .png, atau .jpeg',
            'pelanggan_data_file.max'   => 'Ukuran file maksimal 2MB',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal memperbarui data pelanggan!';
    }
}