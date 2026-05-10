<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peralatan extends Model
{
    use HasFactory;

    protected $table = 'peralatan';
    protected $primaryKey = 'peralatan_id';

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'stok',
        'total_aset',
        'kategori',
    ];

    // Relasi: Satu alat bisa ada di banyak detail peminjaman
    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjamanAlat::class, 'peralatan_id', 'peralatan_id');
    }
}
