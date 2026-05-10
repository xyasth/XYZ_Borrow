<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman indeks laporan (Poin 6)
     */
    public function index()
    {
        // Mengambil semua data peminjaman beserta relasinya untuk ditampilkan di tabel
        $data = Peminjaman::with(['user', 'ruang', 'detailAlat.peralatan'])
                ->orderBy('tanggal_penggunaan', 'desc')
                ->get();

        return view('reports.index', compact('data'));
    }

    /**
     * Ekspor seluruh rekap peminjaman ke PDF
     */
    public function exportRekap()
    {
        $data = Peminjaman::with(['user', 'ruang', 'detailAlat.peralatan'])->get();

        // Memuat view khusus template PDF
        $pdf = Pdf::loadView('reports.rekap_pdf', compact('data'));

        return $pdf->download('rekap-peminjaman-universitas-xyz.pdf');
    }

    /**
     * Ekspor satu detail peminjaman (Invoice/Bukti Pinjam) ke PDF
     */
    public function exportPdf($id)
    {
        $peminjaman = Peminjaman::with(['user', 'ruang', 'detailAlat.peralatan'])->findOrFail($id);

        $pdf = Pdf::loadView('peminjaman.pdf_detail', compact('peminjaman'));

        return $pdf->download('bukti-pinjam-'.$id.'.pdf');
    }
}
