@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Laporan Peminjaman</h2>
    <div class="btn-group">
        <a href="{{ route('reports.pdf') }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export Rekap PDF
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-muted">Histori Seluruh Transaksi</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Peminjam</th>
                        <th>Ruang</th>
                        <th>Tanggal Pakai</th>
                        <th>Status</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $p)
                    <tr>
                        <td class="ps-3">#{{ $p->peminjaman_id }}</td>
                        <td>
                            <div class="fw-bold">{{ $p->user->nama }}</div>
                            <small class="text-muted">{{ $p->user->nomor_induk }}</small>
                        </td>
                        <td>{{ $p->ruang->nama }}</td>
                        <td>
                            <div>{{ $p->tanggal_penggunaan->format('d M Y') }}</div>
                            <small class="text-muted">{{ $p->durasi }} Jam</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $p->status == 'finished' ? 'bg-success' : 'bg-secondary' }}">
                                {{ strtoupper($p->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('peminjaman.pdf', $p->peminjaman_id) }}" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-file-pdf"></i> Bukti
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data peminjaman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
