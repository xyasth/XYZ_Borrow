<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Hanya ambil user dengan role mahasiswa dan dosen untuk manajemen peminjam
        $users = \App\Models\User::whereIn('jenis_akun', ['mahasiswa', 'dosen'])->get();
        return view('users.index', compact('users'));
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


        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_induk' => 'required|unique:users,nomor_induk', // NIM atau NIK
            'nomor_hp' => 'required|string',
            'jenis_akun' => 'required|in:mahasiswa,dosen,admin',
            'password' => 'required|min:6'
        ]);

        $validated['password'] = Hash::make($request->password);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Peminjam berhasil didaftarkan.');
    }

    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'nomor_induk' => 'required|unique:users,nomor_induk,' . $id . ',user_id',
            'nomor_hp' => 'required',
            'jenis_akun' => 'required|in:mahasiswa,dosen,admin',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Data peminjam diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Proteksi: Jangan hapus user yang punya riwayat peminjaman
        if ($user->peminjaman()->exists()) {
            return redirect()->back()->with('error', 'User tidak bisa dihapus karena memiliki riwayat transaksi.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
