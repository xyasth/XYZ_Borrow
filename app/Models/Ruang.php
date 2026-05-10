<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ruang extends Model
{
    use HasFactory;

    protected $table = 'ruang';
    protected $primaryKey = 'ruang_id';

    protected $fillable = [
        'nama',
        'kapasitas',
        'lantai',
        'gedung',
        'status',
    ];

    // Relasi: Satu ruang bisa muncul di banyak transaksi peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'ruang_id', 'ruang_id');
    }
}
