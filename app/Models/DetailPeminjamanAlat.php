<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPeminjamanAlat extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman_alat';
    protected $primaryKey = 'peminjaman_peralatan_id';

    protected $fillable = [
        'peminjaman_id',
        'peralatan_id',
        'jumlah_pinjam',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id', 'peminjaman_id');
    }

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_id', 'peralatan_id');
    }
}
