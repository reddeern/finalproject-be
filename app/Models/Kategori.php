<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori';
    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'kategori_nama',
    ];

    public function alat()
    {
        return $this->hasMany(Alat::class, 'alat_kategori_id', 'kategori_id');
    }
}