<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'email',
        'nomor_induk',
        'nomor_hp',
        'jenis_akun',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: Satu user bisa punya banyak peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'user_id', 'user_id');
    }
}
