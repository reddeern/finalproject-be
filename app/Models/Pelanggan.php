<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pelanggan extends Authenticatable implements AuthenticatableContract, JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggan';
    protected $primaryKey = 'pelanggan_id';

    protected $fillable = [
        'pelanggan_nama',
        'pelanggan_alamat',
        'pelanggan_notelp',
        'pelanggan_email',
        'pelanggan_password',
    ];

    protected $hidden = [
        'pelanggan_password',
    ];

    public function getAuthPassword()
    {
        return $this->pelanggan_password;
    }

    public function setPelangganPasswordAttribute(string $value): void
    {
        $this->attributes['pelanggan_password'] = bcrypt($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => 'pelanggan',
        ];
    }

    public function pelangganData()
    {
        return $this->hasMany(PelangganData::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }

    public function penyewaan()
    {
        return $this->hasMany(Penyewaan::class, 'penyewaan_pelanggan_id', 'pelanggan_id');
    }
}