<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Ruang;
use App\Models\Peralatan;
use App\Models\DetailPeminjamanAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{

    /**
     * Menampilkan daftar semua transaksi peminjaman (Poin 5)
     */
    public function index()
    {
        // Eager loading user dan ruang untuk optimasi query
        $peminjaman = Peminjaman::with(['user', 'ruang'])->orderBy('created_at', 'desc')->get();
        return view('peminjaman.index', compact('peminjaman'));
    }

    /**
     * Menampilkan form untuk membuat peminjaman baru (Poin 4)
     */
    public function create()
    {
        $users = User::all();
        $ruang = Ruang::where('status', 'available')->get();
        $peralatan = Peralatan::where('stok', '>', 0)->get();

        return view('peminjaman.create', compact('users', 'ruang', 'peralatan'));
    }

    /**
     * Menampilkan detail satu transaksi peminjaman
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['user', 'ruang', 'detailAlat.peralatan'])->findOrFail($id);
        return view('peminjaman.show', compact('peminjaman'));
    }
    public function store(Request $request)
    {

        if ($request->has('peralatan')) {
            $cleanedPeralatan = array_filter($request->peralatan, function ($item) {
                return !empty($item['id']) && !empty($item['jumlah']); // Hanya ambil yang diisi
            });

            if (empty($cleanedPeralatan)) {
                // Hapus sepenuhnya dari request jika kosong
                $request->request->remove('peralatan');
            } else {
                // Timpa dengan data yang sudah bersih dari baris kosong
                $request->merge(['peralatan' => $cleanedPeralatan]);
            }
        }
        // Validasi dasar tanpa mewajibkan peralatan di awal
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|integer',
            'jam_selesai' => 'required|integer|gt:jam_mulai',
            'ruang_id' => 'nullable|exists:ruang,ruang_id|required_without:peralatan',
            'peralatan' => 'nullable|array',
            'peralatan.*.id' => 'required_with:peralatan|exists:peralatan,peralatan_id',
            'peralatan.*.jumlah' => 'required_with:peralatan|integer|min:1'
        ]);

        // Kalkulasi Waktu & Durasi
        $jamMulai = (int) $request->jam_mulai;
        $jamSelesai = (int) $request->jam_selesai;

        $mulai = \Carbon\Carbon::parse($request->tanggal)->setHour($jamMulai)->startOfHour();
        $selesai = \Carbon\Carbon::parse($request->tanggal)->setHour($jamSelesai)->startOfHour();
        $durasi = $jamSelesai - $jamMulai;

        DB::beginTransaction();
        try {
            // Logika Overlap (Gunakan $mulai dan $selesai hasil kalkulasi di atas)
            $isOverlapping = function ($query) use ($mulai, $selesai) {
                $query->whereIn('status', ['pending', 'approved'])
                    ->where('tanggal_penggunaan', '<', $selesai)
                    ->whereRaw("DATETIME(tanggal_penggunaan, '+' || durasi || ' hours') > ?", [$mulai->toDateTimeString()]);
            };

            // 1. Cek Ruang
            if ($request->ruang_id) {
                if (Peminjaman::where('ruang_id', $request->ruang_id)->where($isOverlapping)->exists()) {
                    throw new \Exception("Ruang sudah terisi pada jam tersebut.");
                }
            }

            // 2. Cek Peralatan (Hanya jika diisi)
            if ($request->filled('peralatan')) {
                foreach ($request->peralatan as $item) {
                    if (empty($item['id'])) continue; // Abaikan jika baris kosong

                    $alat = Peralatan::lockForUpdate()->find($item['id']);
                    $dipinjamY = DetailPeminjamanAlat::where('peralatan_id', $item['id'])
                        ->whereHas('peminjaman', $isOverlapping)
                        ->sum('jumlah_pinjam');

                    if ($item['jumlah'] > ($alat->total_aset - $dipinjamY)) {
                        throw new \Exception("Stok {$alat->nama_alat} tidak cukup untuk jam ini.");
                    }
                }
            }

            // Simpan
            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'ruang_id' => $request->ruang_id,
                'tanggal_pengajuan' => now(),
                'tanggal_penggunaan' => $mulai,
                'durasi' => $durasi,
                'status' => 'pending',
                'keterangan' => $request->keterangan
            ]);

            if ($request->filled('peralatan')) {
                foreach ($request->peralatan as $item) {
                    if (!empty($item['id'])) {
                        DetailPeminjamanAlat::create([
                            'peminjaman_id' => $peminjaman->peminjaman_id,
                            'peralatan_id' => $item['id'],
                            'jumlah_pinjam' => $item['jumlah']
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('user.dashboard')->with('success', 'Peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([$e->getMessage()]);
        }
    }

    // Update Status oleh Admin
    // app/Http/Controllers/PeminjamanController.php

    public function updateStatus(Request $request, $id)
    {
        $peminjaman = \App\Models\Peminjaman::findOrFail($id);

        // Validasi: Catatan wajib diisi jika statusnya 'rejected'
        $request->validate([
            'status' => 'required|in:approved,rejected,finished',
            'catatan_admin' => 'nullable|string|required_if:status,rejected'
        ], [
            'catatan_admin.required_if' => 'Alasan penolakan wajib diisi.'
        ]);

        // Update data
        $peminjaman->status = $request->status;
        $peminjaman->catatan_admin = $request->catatan_admin;

        // Jika status selesai, catat waktu kembali otomatis
        if ($request->status === 'finished') {
            $peminjaman->waktu_kembali = now();
        }

        $peminjaman->save();

        return back()->with('success', "Status transaksi #" . $id . " berhasil diubah menjadi " . strtoupper($request->status));
    }
    public function riwayat()
    {
        // Mengambil data peminjaman khusus untuk user yang sedang login
        $myPeminjaman = \App\Models\Peminjaman::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->with('ruang')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.dashboard', compact('myPeminjaman')); // Pastikan file dashboard.blade.php diletakkan di folder resources/views/user/
    }

    public function checkAvailability(Request $request)
    {
        $mulai = \Carbon\Carbon::parse($request->tanggal)->setHour((int)$request->jam_mulai)->startOfHour();
        $selesai = \Carbon\Carbon::parse($request->tanggal)->setHour((int)$request->jam_selesai)->startOfHour();
        $durasi = (int)$request->jam_selesai - (int)$request->jam_mulai;

        // Logika Overlap
        $isOverlapping = function ($query) use ($mulai, $selesai) {
            $query->whereIn('status', ['pending', 'approved'])
                ->where('tanggal_penggunaan', '<', $selesai)
                ->whereRaw("DATETIME(tanggal_penggunaan, '+' || durasi || ' hours') > ?", [$mulai->toDateTimeString()]);
        };

        // 1. Cek Ruang
        $ruang = \App\Models\Ruang::all()->map(function ($r) use ($isOverlapping) {
            $terpakai = \App\Models\Peminjaman::where('ruang_id', $r->ruang_id)->where($isOverlapping)->exists();

            $statusTeks = 'Available';
            $isDisabled = false;

            if ($r->status === 'maintenance') {
                $statusTeks = 'Maintenance';
                $isDisabled = true;
            } elseif ($terpakai) {
                $statusTeks = 'Unavailable (Sudah dipesan)';
                $isDisabled = true;
            }

            return [
                'id' => $r->ruang_id,
                'nama' => "{$r->nama} | {$statusTeks} (Gedung {$r->gedung}, Lt. {$r->lantai})",
                'disabled' => $isDisabled
            ];
        });

        // 2. Cek Stok Peralatan
        $peralatan = \App\Models\Peralatan::all()->map(function ($p) use ($isOverlapping) {
            $jumlahDipinjam = \App\Models\DetailPeminjamanAlat::where('peralatan_id', $p->peralatan_id)
                ->whereHas('peminjaman', $isOverlapping)
                ->sum('jumlah_pinjam');

            $sisa = $p->total_aset - $jumlahDipinjam;

            return [
                'id' => $p->peralatan_id,
                'nama' => "{$p->nama_alat} (Sisa: {$sisa})",
                'sisa' => $sisa
            ];
        });

        return response()->json([
            'ruang' => $ruang,
            'peralatan' => $peralatan
        ]);
    }
}
