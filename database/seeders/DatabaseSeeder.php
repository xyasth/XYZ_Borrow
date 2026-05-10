<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ruang;
use App\Models\Peralatan;
use App\Models\Peminjaman;
use App\Models\DetailPeminjamanAlat;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin, Dosen, Mahasiswa)
        $admin = User::create([
            'nama' => 'Admin Sistem',
            'email' => 'admin@xyz.ac.id',
            'nomor_induk' => '19900101',
            'nomor_hp' => '08123456789',
            'jenis_akun' => 'admin',
            'password' => Hash::make('123'),
        ]);

        $dosen = User::create([
            'nama' => 'Dr. Budi Santoso',
            'email' => 'budi@xyz.ac.id',
            'nomor_induk' => '19850512',
            'nomor_hp' => '08129999888',
            'jenis_akun' => 'dosen',
            'password' => Hash::make('123'),
        ]);

        $mhs = User::create([
            'nama' => 'Siska Putri',
            'email' => 'siska@student.xyz.ac.id',
            'nomor_induk' => '20220001',
            'nomor_hp' => '08571111222',
            'jenis_akun' => 'mahasiswa',
            'password' => Hash::make('password123'),
        ]);

        // 2. Seed Ruang
        $lab = Ruang::create([
            'nama' => 'Lab Komputer A',
            'kapasitas' => 40,
            'lantai' => '2',
            'gedung' => 'gedung_a',
            'status' => 'available',
        ]);

        $aula = Ruang::create([
            'nama' => 'Aula Utama',
            'kapasitas' => 200,
            'lantai' => '1',
            'gedung' => 'gedung_b',
            'status' => 'available',
        ]);

        // 3. Seed Peralatan
        $proyektor = Peralatan::create([
            'kode_alat' => 'PRJ-001',
            'nama_alat' => 'Proyektor Epson',
            'stok' => 5, // Stok saat ini (dinamis)
            'total_aset' => 5,
            'kategori' => 'Elektronik',
        ]);

        $kamera = Peralatan::create([
            'kode_alat' => 'CAM-001',
            'nama_alat' => 'Kamera Sony Mirrorless',
            'stok' => 3,
            'total_aset' => 3,
            'kategori' => 'Multimedia',
        ]);

        // 4. Seed Transaksi Peminjaman (Contoh: Transaksi Selesai)
        $p1 = Peminjaman::create([
            'user_id' => $dosen->user_id,
            'ruang_id' => $lab->ruang_id,
            'tanggal_pengajuan' => Carbon::now()->subDays(5),
            'tanggal_penggunaan' => Carbon::now()->subDays(2)->setHour(10)->startOfHour(),
            'durasi' => 3,
            'status' => 'finished',
            'waktu_kembali' => Carbon::now()->subDays(2)->setHour(13)->startOfHour(),
            'keterangan' => 'Praktikum Pemrograman Web',
            'catatan_admin' => 'Disetujui, harap jaga kebersihan.',
        ]);

        // Tambah Detail Alat untuk Peminjaman 1
        DetailPeminjamanAlat::create([
            'peminjaman_id' => $p1->peminjaman_id,
            'peralatan_id' => $proyektor->peralatan_id,
            'jumlah_pinjam' => 1,
        ]);

        // 5. Seed Transaksi Peminjaman (Contoh: Transaksi Menunggu/Pending)
        $p2 = Peminjaman::create([
            'user_id' => $mhs->user_id,
            'ruang_id' => $aula->ruang_id,
            'tanggal_pengajuan' => Carbon::now()->subDay(),
            'tanggal_penggunaan' => Carbon::now()->addDays(2)->setHour(14)->startOfHour(),
            'durasi' => 5,
            'status' => 'pending',
            'keterangan' => 'Seminar Himpunan Mahasiswa',
        ]);

        // Tambah Detail Alat untuk Peminjaman 2
        DetailPeminjamanAlat::create([
            'peminjaman_id' => $p2->peminjaman_id,
            'peralatan_id' => $proyektor->peralatan_id,
            'jumlah_pinjam' => 2,
        ]);

        DetailPeminjamanAlat::create([
            'peminjaman_id' => $p2->peminjaman_id,
            'peralatan_id' => $kamera->peralatan_id,
            'jumlah_pinjam' => 1,
        ]);
    }
}
