<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

        public function register(RegisterRequest $request)
{
    try {
        $admin = Admin::create([
            'admin_username' => $request->admin_username,
            'admin_password' => $request->password, // otomatis di-hash oleh mutator di model
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil didaftarkan',
            'data' => null,
        ], 201);
        
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'There error in Internal Server',
            'data'    => null,
            'errors'  => $e->getMessage(),
        ], 500);
    }
}

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_username' => 'required|string',
            'password'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan login!',
                'data'    => null,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $credentials = [
            'admin_username' => $request->admin_username,
            'password'       => $request->password,
        ];

        try {
            if (! $token = Auth::guard('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah',
                    'data'    => null,
                ], 401);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }

        return $this->respondWithToken($token, 'Login berhasil');
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'message' => 'Successfully get admin data',
            'data'    => Auth::guard('api')->user(),
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
            'data'    => null,
        ], 200);
    }

    public function refresh()
    {
        try {
            $token = Auth::guard('api')->refresh();
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kedaluwarsa, silakan login ulang',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 401);
        }

        return $this->respondWithToken($token, 'Token berhasil diperbarui');
    }

    protected function respondWithToken(string $token, string $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
                'admin'        => Auth::guard('api')->user(),
            ],
        ], 200);
    }
}