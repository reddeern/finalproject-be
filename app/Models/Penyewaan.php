<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyewaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penyewaan';
    protected $primaryKey = 'penyewaan_id';

    protected $fillable = [
        'penyewaan_pelanggan_id',
        'penyewaan_tglsewa',
        'penyewaan_tglkembali',
        'penyewaan_sttspembayaran',
        'penyewaan_sttskembali',
        'penyewaan_totalharga',
    ];

    protected $attributes = [
        'penyewaan_sttspembayaran' => 'Belum Dibayar',
        'penyewaan_sttskembali' => 'Belum Kembali',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'penyewaan_pelanggan_id', 'pelanggan_id');
    }

    public function detail()
    {
        return $this->hasMany(PenyewaanDetail::class, 'penyewaan_detail_penyewaan_id', 'penyewaan_id');
    }
}