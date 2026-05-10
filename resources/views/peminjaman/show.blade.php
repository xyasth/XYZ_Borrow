@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h5 class="mb-0">Detail Peminjaman #{{ $peminjaman->peminjaman_id }}</h5>
            <span class="badge bg-primary">{{ strtoupper($peminjaman->status) }}</span>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Informasi Peminjam</h6>
                    <table class="table table-sm table-borderless">
                        <tr><td>Nama</td><td>: {{ $peminjaman->user->nama }}</td></tr>
                        <tr><td>ID/NIM</td><td>: {{ $peminjaman->user->nomor_induk }}</td></tr>
                        <tr><td>Kegiatan</td><td>: <strong>{{ $peminjaman->keterangan }}</strong></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Informasi Waktu & Lokasi</h6>
                    <table class="table table-sm table-borderless">
                        <tr><td>Ruang</td><td>: {{ $peminjaman->ruang->nama }} (Gedung {{ strtoupper($peminjaman->ruang->gedung) }})</td></tr>
                        <tr><td>Waktu Pakai</td><td>: {{ $peminjaman->tanggal_penggunaan->format('d M Y, H:i') }}</td></tr>
                        <tr><td>Durasi</td><td>: {{ $peminjaman->durasi }} Jam</td></tr>
                    </table>
                </div>
            </div>

            <h6>Peralatan yang Dipinjam:</h6>
            <table class="table table-bordered bg-light">
                <thead class="table-secondary">
                    <tr><th>Nama Alat</th><th>Kode</th><th>Jumlah</th></tr>
                </thead>
                <tbody>
                    @foreach($peminjaman->detailAlat as $item)
                    <tr>
                        <td>{{ $item->peralatan->nama_alat }}</td>
                        <td><code>{{ $item->peralatan->kode_alat }}</code></td>
                        <td>{{ $item->jumlah_pinjam }} Unit</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 p-3 bg-light border">
                <p class="mb-1 text-muted small">Catatan Admin:</p>
                <p>{{ $peminjaman->catatan_admin ?? 'Tidak ada catatan.' }}</p>
                @if($peminjaman->waktu_kembali)
                    <p class="mb-0 text-success small">Dikembalikan pada: {{ $peminjaman->waktu_kembali->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="/peminjaman" class="btn btn-secondary">Kembali</a>
            <a href="/peminjaman/{{ $peminjaman->peminjaman_id }}/pdf" class="btn btn-danger">Cetak Bukti (PDF)</a>
        </div>
    </div>
</div>
@endsection
