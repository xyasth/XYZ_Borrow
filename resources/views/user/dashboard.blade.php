@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between">
    <h2>Riwayat Peminjaman Anda</h2>
    <a href="/peminjaman/create" class="btn btn-primary">Buat Peminjaman Baru</a>
</div>
<table class="table mt-3">
    <thead><tr><th>Ruang/Alat</th><th>Tanggal Pakai</th><th>Status</th><th>Catatan Admin</th></tr></thead>
    <tbody>
        @foreach($myPeminjaman as $p)
        <tr>
            <td>{{ $p->ruang->nama ?? 'Hanya Alat' }}</td>
            <td>{{ $p->tanggal_penggunaan }}</td>
            <td>{{ $p->status }}</td>
            <td>{{ $p->catatan_admin }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
