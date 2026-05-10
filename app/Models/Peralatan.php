<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahan Tipe Relasi

class Peralatan extends Model
{
    use HasFactory;

    protected $table = 'peralatan';
    protected $primaryKey = 'peralatan_id';

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'total_aset', // 'stok' dihapus
        'kategori',
    ];

    public function detailPeminjaman(): HasMany
    {
        return $this->hasMany(DetailPeminjamanAlat::class, 'peralatan_id', 'peralatan_id');
    }
}
