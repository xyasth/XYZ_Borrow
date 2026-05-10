<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;

class RuangController extends Controller
{
    public function index()
    {
        $ruang = Ruang::all();
        return view('ruang.index', compact('ruang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'kapasitas' => 'required|integer|min:1',
            'lantai' => 'required|string',
            'gedung' => 'required|in:gedung_a,gedung_b',
            'status' => 'required|in:available,unavailable,maintenance'
        ]);

        Ruang::create($validated);
        return redirect()->route('ruang.index')->with('success', 'Ruang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $ruang = \App\Models\Ruang::findOrFail($id);
        return view('ruang.edit', compact('ruang'));
    }

    public function update(Request $request, $id)
    {
        $ruang = Ruang::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string',
            'kapasitas' => 'required|integer|min:1',
            'lantai' => 'required|string',
            'gedung' => 'required|in:gedung_a,gedung_b',
            'status' => 'required|in:available,unavailable,maintenance'
        ]);

        $ruang->update($validated);
        return redirect()->route('ruang.index')->with('success', 'Data ruang diperbarui.');
    }

    public function destroy($id)
    {
        $ruang = Ruang::findOrFail($id);
        if ($ruang->peminjaman()->exists()) {
            return redirect()->back()->with('error', 'Ruang tidak bisa dihapus karena memiliki riwayat peminjaman.');
        }
        $ruang->delete();
        return redirect()->route('ruang.index')->with('success', 'Ruang berhasil dihapus.');
    }
}
