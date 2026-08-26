<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pelanggan\RegisterPelangganRequest;
use App\Models\Pelanggan;
use App\Models\PelangganData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PelangganAuthController extends Controller
{
    /**
     * POST /api/pelanggan/register
     * Menerima multipart/form-data karena ada file upload opsional.
     */
    public function register(RegisterPelangganRequest $request)
    {
        try {
            $pelanggan = DB::transaction(function () use ($request): Pelanggan {
                $pelanggan = Pelanggan::create([
                    'pelanggan_nama'     => $request->pelanggan_nama,
                    'pelanggan_alamat'   => $request->pelanggan_alamat,
                    'pelanggan_notelp'   => $request->pelanggan_notelp,
                    'pelanggan_email'    => $request->pelanggan_email,
                    'pelanggan_password' => $request->password,
                ]);

                if ($request->hasFile('pelanggan_data_file')) {
                    $path = $request->file('pelanggan_data_file')->store('pelanggan_data', 'public');

                    PelangganData::create([
                        'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
                        'pelanggan_data_jenis'        => $request->pelanggan_data_jenis,
                        'pelanggan_data_file'         => $path,
                    ]);
                }

                return $pelanggan;
            });

            $token = Auth::guard('pelanggan-api')->login($pelanggan);

            return $this->respondWithToken($pelanggan, $token, 'Registrasi berhasil');
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/pelanggan/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelanggan_email' => 'required|email',
            'password'        => 'required|string',
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
            'pelanggan_email' => $request->pelanggan_email,
            'password'        => $request->password,
        ];

        if (! $token = Auth::guard('pelanggan-api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
                'data'    => null,
            ], 401);
        }

        $pelanggan = Auth::guard('pelanggan-api')->user();

        return $this->respondWithToken($pelanggan, $token, 'Login berhasil');
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'message' => 'Successfully get pelanggan data',
            'data'    => Auth::guard('pelanggan-api')->user()->load('pelangganData'),
        ], 200);
    }

    public function logout()
    {
        Auth::guard('pelanggan-api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
            'data'    => null,
        ], 200);
    }

    protected function respondWithToken(Pelanggan $pelanggan, string $token, string $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => Auth::guard('pelanggan-api')->factory()->getTTL() * 60,
                'pelanggan'    => $pelanggan,
            ],
        ], 200);
    }
}