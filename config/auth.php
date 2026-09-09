<?php

use App\Models\Admin;
use App\Models\Pelanggan;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'admins'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [

        // Guard untuk ADMIN
        'api' => [
            'driver' => 'jwt',
            'provider' => 'admins',
        ],

        // Guard untuk PELANGGAN
        'pelanggan-api' => [
            'driver' => 'jwt',
            'provider' => 'pelanggans',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        // Provider untuk ADMIN
        'admins' => [
            'driver' => 'eloquent',
            'model' => Admin::class,
        ],

        // Provider untuk PELANGGAN
        'pelanggans' => [
            'driver' => 'eloquent',
            'model' => Pelanggan::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [

        // Reset password ADMIN
        'admins' => [
            'provider' => 'admins',
            'table' => env(
                'AUTH_PASSWORD_RESET_TOKEN_TABLE',
                'password_reset_tokens'
            ),
            'expire' => 60,
            'throttle' => 60,
        ],

        // Reset password PELANGGAN
        'pelanggans' => [
            'provider' => 'pelanggans',
            'table' => env(
                'AUTH_PASSWORD_RESET_TOKEN_TABLE',
                'password_reset_tokens'
            ),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];