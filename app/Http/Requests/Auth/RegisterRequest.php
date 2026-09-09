<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseApiRequest;

class RegisterRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'admin_username' => 'required|string|max:50|unique:admin,admin_username',
            'password'       => 'required|string|min:6', //|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'admin_username.unique' => 'Username sudah digunakan, silakan pilih username lain',
            'password.min'          => 'Password minimal 6 karakter',
            //'password.confirmed'    => 'Konfirmasi password tidak cocok',
        ];
    }

    protected function failedMessage(): string
    {
        return 'Gagal melakukan registrasi!';
    }
}