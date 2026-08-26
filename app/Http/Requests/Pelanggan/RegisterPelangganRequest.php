<?php

namespace App\Http\Requests\Pelanggan;

use App\Http\Requests\BaseApiRequest;

class RegisterPelangganRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pelanggan_nama'         => 'required|string|max:150',
            'pelanggan_alamat'       => 'required|string|max:200',
            'pelanggan_notelp'       => 'required|string|max:13',
            'pelanggan_email'        => 'required|email|max:100|unique:pelanggan,pelanggan_email',
            'password'               => 'required|string|min:6|confirmed',

            // Dokumen KTP/SIM bersifat OPSIONAL saat register,
            // bisa juga diupload belakangan lewat endpoint pelanggan-data terpisah
            'pelanggan_data_jenis'   => 'sometimes|required_with:pelanggan_data_file|in:KTP,SIM',
            'pelanggan_data_file'    => 'sometimes|file|mimes:jpg,png,jpeg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_email.unique'    => 'Email sudah terdaftar, silakan login',
            'password.confirmed'        => 'Konfirmasi password tidak cocok',
            'pelanggan_data_file.mimes' => 'File harus memiliki format .jpg, .png, atau .jpeg',
            'pelanggan_data_file.max'   => 'Ukuran file maksimal 2MB',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal melakukan registrasi!';
    }
}