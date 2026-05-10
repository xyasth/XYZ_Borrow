<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('jenis_akun', ['mahasiswa', 'dosen'])->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        // LOGIKA PERALATAN DIHAPUS SEPENUHNYA KARENA BUKAN TANGGUNG JAWAB USERCONTROLLER

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_induk' => 'required|unique:users,nomor_induk',
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
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Buat array rules dasar
        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'nomor_induk' => 'required|unique:users,nomor_induk,' . $id . ',user_id',
            'nomor_hp' => 'required|string',
            'jenis_akun' => 'required|in:mahasiswa,dosen,admin',
        ];

        // PERBAIKAN CELAH KEAMANAN: Masukkan validasi password ke dalam rules jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6';
        }

        $validated = $request->validate($rules);

        // Jika password lolos validasi (ada dan >= 6 karakter), lakukan hashing
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Data peminjam diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->peminjaman()->exists()) {
            return redirect()->back()->with('error', 'User tidak bisa dihapus karena memiliki riwayat transaksi.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
