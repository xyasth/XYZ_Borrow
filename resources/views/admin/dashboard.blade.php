@extends('layouts.app')
@section('content')
<h2>Monitoring Peminjaman (Admin)</h2>
<table class="table">
    <thead><tr><th>Peminjam</th><th>Waktu</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
        @foreach($peminjaman as $p)
        <tr>
            <td>{{ $p->user->nama }}</td>
            <td>{{ $p->tanggal_penggunaan }}</td>
            <td><span class="badge">{{ $p->status }}</span></td>
            <td>
                @if($p->status == 'pending')
                <form action="/peminjaman/{{$p->peminjaman_id}}/status" method="POST">
                    @csrf @method('PATCH')
                    <input type="text" name="catatan_admin" placeholder="Alasan jika reject">
                    <button name="status" value="approved" class="btn btn-success">Setujui</button>
                    <button name="status" value="rejected" class="btn btn-danger">Tolak</button>
                </form>
                @elseif($p->status == 'approved')
                <form action="/peminjaman/{{$p->peminjaman_id}}/status" method="POST">
                    @csrf @method('PATCH')
                    <button name="status" value="finished" class="btn btn-primary">Selesai</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
