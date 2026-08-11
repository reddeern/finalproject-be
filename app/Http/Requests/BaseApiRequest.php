<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = [];
        foreach ($validator->errors()->messages() as $field => $messages) {
            $errors[] = [$field => $messages[0]];
        }

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $this->failedMessage(),
            'data'    => null,
            'errors'  => $errors,
        ], 422));
    }

    protected function failedMessage(): string
    {
        return 'Validasi gagal';
    }
}