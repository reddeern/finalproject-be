<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PelangganData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggan_data';
    protected $primaryKey = 'pelanggan_data_id';

    protected $fillable = [
        'pelanggan_data_pelanggan_id',
        'pelanggan_data_jenis',
        'pelanggan_data_file',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }
}