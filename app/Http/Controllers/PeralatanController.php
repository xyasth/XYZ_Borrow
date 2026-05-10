<?php

namespace App\Http\Controllers;

use App\Models\Peralatan;
use Illuminate\Http\Request;

class PeralatanController extends Controller
{
    public function index()
    {
        $peralatan = Peralatan::all();
        return view('peralatan.index', compact('peralatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_alat' => 'required|unique:peralatan,kode_alat',
            'nama_alat' => 'required|string',
            'total_aset' => 'required|integer|min:1',
            'kategori' => 'required|string'
        ]);

        Peralatan::create($validated);
        return redirect()->route('peralatan.index')->with('success', 'Alat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $alat = \App\Models\Peralatan::findOrFail($id);
        return view('peralatan.edit', compact('alat'));
    }

    public function update(Request $request, $id)
    {
        $alat = Peralatan::findOrFail($id);
        $validated = $request->validate([
            'kode_alat' => 'required|unique:peralatan,kode_alat,' . $id . ',peralatan_id',
            'nama_alat' => 'required|string',
            'total_aset' => 'required|integer|min:1',
            'kategori' => 'required|string'
        ]);

        $alat->update($validated);
        return redirect()->route('peralatan.index')->with('success', 'Data alat diperbarui.');
    }

    public function destroy($id)
    {
        $alat = Peralatan::findOrFail($id);
        // Validasi agar alat yang sedang dipinjam tidak bisa dihapus
        if ($alat->detailPeminjaman()->exists()) {
            return redirect()->back()->with('error', 'Alat masih terkait dengan data transaksi peminjaman.');
        }
        $alat->delete();
        return redirect()->route('peralatan.index')->with('success', 'Alat berhasil dihapus.');
    }
}
