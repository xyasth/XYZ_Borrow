<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'peminjaman_id';

    protected $fillable = [
        'user_id',
        'ruang_id',
        'tanggal_pengajuan',
        'tanggal_penggunaan',
        'durasi',
        'status',
        'waktu_kembali',
        'keterangan',
        'catatan_admin',
    ];

    // Cast tanggal agar otomatis menjadi objek Carbon
    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_penggunaan' => 'datetime',
        'waktu_kembali' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'ruang_id', 'ruang_id');
    }

    public function detailAlat()
    {
        return $this->hasMany(DetailPeminjamanAlat::class, 'peminjaman_id', 'peminjaman_id');
    }
}
