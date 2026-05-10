<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ruang;
use App\Models\Peralatan;
use App\Models\Peminjaman;
use App\Models\DetailPeminjamanAlat;
use Carbon\Carbon;

class PeminjamanCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $mahasiswa;
    protected $dosen;
    protected $ruangA;
    protected $proyektor;

    /**
     * Menyiapkan data awal (seeding terisolasi) yang dibutuhkan untuk seluruh skenario pengujian.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin@test.com',
            'nomor_induk' => '111',
            'nomor_hp' => '111',
            'jenis_akun' => 'admin',
            'password' => bcrypt('password')
        ]);

        $this->mahasiswa = User::create([
            'nama' => 'Mhs Test',
            'email' => 'mhs@test.com',
            'nomor_induk' => '222',
            'nomor_hp' => '222',
            'jenis_akun' => 'mahasiswa',
            'password' => bcrypt('password')
        ]);

        $this->dosen = User::create([
            'nama' => 'Dosen Test',
            'email' => 'dosen@test.com',
            'nomor_induk' => '333',
            'nomor_hp' => '333',
            'jenis_akun' => 'dosen',
            'password' => bcrypt('password')
        ]);

        $this->ruangA = Ruang::create([
            'nama' => 'Ruang A',
            'kapasitas' => 50,
            'lantai' => '1',
            'gedung' => 'gedung_a',
            'status' => 'available'
        ]);

        $this->proyektor = Peralatan::create([
            'kode_alat' => 'PRJ-01',
            'nama_alat' => 'Proyektor',
            'stok' => 5,
            'total_aset' => 5,
            'kategori' => 'Elektronik'
        ]);
    }

    /**
     * Menguji penolakan sistem saat formulir dikirim tanpa mengisi field wajib.
     */
    public function test_pengajuan_gagal_ketika_field_wajib_kosong()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', []);

        $response->assertSessionHasErrors(['tanggal', 'jam_mulai', 'jam_selesai']);
    }

    /**
     * Menguji penolakan sistem saat format input tanggal tidak sesuai standar yang diizinkan.
     */
    public function test_pengajuan_gagal_ketika_format_tanggal_tidak_valid()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => 'Besok pagi',
            'jam_mulai' => 10,
            'jam_selesai' => 12
        ]);

        $response->assertSessionHasErrors(['tanggal']);
    }

    /**
     * Menguji penolakan sistem saat pengguna mencoba meminjam untuk tanggal yang sudah terlewat.
     */
    public function test_pengajuan_gagal_ketika_memilih_tanggal_di_masa_lalu()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::yesterday()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12
        ]);

        $response->assertSessionHasErrors(['tanggal']);
    }

    /**
     * Menguji penolakan sistem saat jam selesai sama dengan jam mulai (durasi berjumlah nol).
     */
    public function test_pengajuan_gagal_ketika_durasi_bernilai_nol()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 10
        ]);

        $response->assertSessionHasErrors(['jam_selesai']);
    }

    /**
     * Menguji penolakan sistem saat jam selesai lebih awal daripada jam mulai (durasi bernilai negatif).
     */
    public function test_pengajuan_gagal_ketika_durasi_bernilai_negatif()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 12,
            'jam_selesai' => 10
        ]);

        $response->assertSessionHasErrors(['jam_selesai']);
    }

    /**
     * Menguji penolakan sistem saat jumlah peralatan yang akan dipinjam bernilai nol atau negatif.
     */
    public function test_pengajuan_gagal_ketika_jumlah_peralatan_kurang_dari_satu()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'peralatan' => [['id' => $this->proyektor->peralatan_id, 'jumlah' => -1]],
            'keterangan' => 'Test'
        ]);

        $response->assertSessionHasErrors(['peralatan.0.jumlah']);
    }

    /**
     * Menguji penolakan sistem saat pengguna tidak memilih ruang maupun peralatan sama sekali.
     */
    public function test_pengajuan_gagal_ketika_tidak_memilih_ruang_dan_peralatan()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'ruang_id' => null,
            'keterangan' => 'Test'
        ]);

        $response->assertSessionHasErrors(['ruang_id']);
    }

    /**
     * Menguji keberhasilan penyimpanan data saat seluruh input bernilai valid dan lengkap.
     */
    public function test_pengajuan_berhasil_dengan_input_yang_valid_dan_lengkap()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'ruang_id' => $this->ruangA->ruang_id,
            'peralatan' => [['id' => $this->proyektor->peralatan_id, 'jumlah' => 1]],
            'keterangan' => 'Test'
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('peminjaman', ['ruang_id' => $this->ruangA->ruang_id]);
    }

    /**
     * Membuat data peminjaman Ruang A (Existing) dari jam 10:00 hingga 13:00 untuk skenario bentrok.
     */
    private function buatJadwalRuangAExisting()
    {
        Peminjaman::create([
            'user_id' => $this->dosen->user_id,
            'ruang_id' => $this->ruangA->ruang_id,
            'tanggal_pengajuan' => now(),
            'tanggal_penggunaan' => Carbon::tomorrow()->setHour(10)->startOfHour(),
            'durasi' => 3,
            'status' => 'approved',
            'keterangan' => 'Booking Existing'
        ]);
    }

    /**
     * Menguji keberhasilan peminjaman ruang saat jadwal baru berada sebelum, sesudah, atau berurutan langsung tanpa beririsan dengan jadwal yang ada.
     */
    public function test_pengajuan_ruang_berhasil_ketika_jadwal_tidak_beririsan()
    {
        $this->buatJadwalRuangAExisting();

        $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 5,
            'jam_selesai' => 7,
            'ruang_id' => $this->ruangA->ruang_id,
            'keterangan' => 'Test 1'
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 14,
            'jam_selesai' => 16,
            'ruang_id' => $this->ruangA->ruang_id,
            'keterangan' => 'Test 2'
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 8,
            'jam_selesai' => 10,
            'ruang_id' => $this->ruangA->ruang_id,
            'keterangan' => 'Test 3'
        ])->assertSessionHasNoErrors();
    }

    /**
     * Menguji penolakan sistem pada berbagai skenario bentrokan waktu (irisan penuh, irisan awal, irisan akhir, di dalam, dan melingkupi).
     */
    public function test_pengajuan_ruang_gagal_ketika_terjadi_bentrokan_jadwal()
    {
        $this->buatJadwalRuangAExisting();

        $bentrokScenarios = [
            [10, 13],
            [9, 11],
            [12, 14],
            [11, 12],
            [9, 14],
        ];

        foreach ($bentrokScenarios as $jam) {
            $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
                'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
                'jam_mulai' => $jam[0],
                'jam_selesai' => $jam[1],
                'ruang_id' => $this->ruangA->ruang_id,
                'keterangan' => 'Test Bentrok'
            ]);
            $response->assertSessionHasErrors();
        }
    }

    /**
     * Menguji keberhasilan peminjaman ruang pada jadwal yang sama jika transaksi sebelumnya telah berstatus dibatalkan atau ditolak.
     */
    public function test_pengajuan_ruang_berhasil_jika_jadwal_yang_bentrok_berstatus_ditolak()
    {
        Peminjaman::create([
            'user_id' => $this->dosen->user_id,
            'ruang_id' => $this->ruangA->ruang_id,
            'tanggal_pengajuan' => now(),
            'tanggal_penggunaan' => Carbon::tomorrow()->setHour(10)->startOfHour(),
            'durasi' => 3,
            'status' => 'rejected',
            'keterangan' => 'Booking Existing'
        ]);

        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 13,
            'ruang_id' => $this->ruangA->ruang_id,
            'keterangan' => 'Test'
        ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * Menguji penolakan sistem saat jumlah alat yang dipinjam melebihi total aset fisik yang dimiliki universitas.
     */
    public function test_pengajuan_peralatan_gagal_ketika_melebihi_total_aset()
    {
        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'peralatan' => [['id' => $this->proyektor->peralatan_id, 'jumlah' => 6]],
            'keterangan' => 'Test Total Aset'
        ]);

        $response->assertSessionHasErrors();
        $this->assertStringContainsString('tidak cukup', session('errors')->first());
    }

    /**
     * Menguji penolakan sistem saat jumlah permintaan alat melebihi sisa stok yang tersedia secara dinamis pada rentang waktu yang beririsan.
     */
    public function test_pengajuan_peralatan_gagal_ketika_sisa_stok_dinamis_tidak_mencukupi()
    {
        $p = Peminjaman::create([
            'user_id' => $this->dosen->user_id,
            'tanggal_pengajuan' => now(),
            'tanggal_penggunaan' => Carbon::tomorrow()->setHour(10)->startOfHour(),
            'durasi' => 2,
            'status' => 'approved',
            'keterangan' => '-'
        ]);
        DetailPeminjamanAlat::create(['peminjaman_id' => $p->peminjaman_id, 'peralatan_id' => $this->proyektor->peralatan_id, 'jumlah_pinjam' => 4]);

        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'peralatan' => [['id' => $this->proyektor->peralatan_id, 'jumlah' => 2]],
            'keterangan' => 'Test Sisa Stok'
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Menguji integritas database (Rollback) agar tidak ada data parsial yang tersimpan jika ketersediaan alat gagal di tengah proses transaksi.
     */
    public function test_transaksi_dibatalkan_sepenuhnya_jika_salah_satu_ketersediaan_gagal()
    {
        $kamera = Peralatan::create([
            'kode_alat' => 'CAM',
            'nama_alat' => 'Kamera',
            'stok' => 1,
            'total_aset' => 1,
            'kategori' => 'M'
        ]);

        $response = $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'ruang_id' => $this->ruangA->ruang_id,
            'peralatan' => [
                ['id' => $kamera->peralatan_id, 'jumlah' => 1],
                ['id' => $this->proyektor->peralatan_id, 'jumlah' => 10]
            ],
            'keterangan' => 'Test Rollback'
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('peminjaman', 0);
        $this->assertDatabaseCount('detail_peminjaman_alat', 0);
    }

    /**
     * Menguji kebenaran alur status: pengajuan baru berstatus pending, mahasiswa ditolak mengubah statusnya, namun admin memiliki wewenang untuk menyetujuinya.
     */
    public function test_alur_persetujuan_hanya_dapat_dilakukan_oleh_admin()
    {
        $this->actingAs($this->mahasiswa)->post('/peminjaman', [
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => 10,
            'jam_selesai' => 12,
            'ruang_id' => $this->ruangA->ruang_id,
            'keterangan' => 'Test Acara'
        ]);

        $peminjaman = Peminjaman::first();
        $this->assertEquals('pending', $peminjaman->status);

        $this->patch("/peminjaman/{$peminjaman->peminjaman_id}/status", ['status' => 'approved'])
            ->assertStatus(403);

        $this->actingAs($this->admin)->patch("/peminjaman/{$peminjaman->peminjaman_id}/status", [
            'status' => 'approved'
        ])->assertSessionHasNoErrors();

        $this->assertEquals('approved', $peminjaman->fresh()->status);
    }

    /**
     * Menguji perlindungan sistem terhadap transisi status yang melompat (Pending ke Finished) atau proses penolakan tanpa menyertakan alasan dari admin.
     */
    public function test_perubahan_status_gagal_jika_melanggar_aturan_mesin_status()
    {
        $p = Peminjaman::create([
            'user_id' => $this->mahasiswa->user_id,
            'tanggal_pengajuan' => now(),
            'tanggal_penggunaan' => Carbon::tomorrow(),
            'durasi' => 2,
            'status' => 'pending',
            'keterangan' => '-'
        ]);

        $this->actingAs($this->admin)->patch("/peminjaman/{$p->peminjaman_id}/status", ['status' => 'finished'])
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch("/peminjaman/{$p->peminjaman_id}/status", ['status' => 'rejected', 'catatan_admin' => ''])
            ->assertSessionHasErrors(['catatan_admin']);
    }

    /**
     * Menguji keamanan privasi data dengan memastikan mahasiswa tidak dapat melihat riwayat peminjaman milik pengguna lain.
     */
    public function test_pengguna_hanya_dapat_melihat_data_peminjaman_miliknya_sendiri()
    {
        Peminjaman::create([
            'user_id' => $this->dosen->user_id,
            'tanggal_pengajuan' => now(),
            'tanggal_penggunaan' => Carbon::tomorrow(),
            'durasi' => 2,
            'status' => 'pending',
            'keterangan' => 'Milik Dosen'
        ]);

        $response = $this->actingAs($this->mahasiswa)->get('/dashboard');

        $response->assertDontSee('Milik Dosen');
    }
}
