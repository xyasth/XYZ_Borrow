@extends('layouts.app')

@section('content')
<div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-header bg-warning text-dark"><h5>Edit Data Peminjam</h5></div>
    <form action="{{ route('users.update', $user->user_id) }}" method="POST" class="card-body">
        @csrf @method('PUT')
        <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama" value="{{ $user->nama }}" class="form-control" required></div>
        <div class="mb-3"><label>NIM / NIK</label><input type="text" name="nomor_induk" value="{{ $user->nomor_induk }}" class="form-control" required></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" value="{{ $user->email }}" class="form-control" required></div>
        <div class="mb-3"><label>Jenis Akun</label>
            <select name="jenis_akun" class="form-select">
                <option value="mahasiswa" {{ $user->jenis_akun == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ $user->jenis_akun == 'dosen' ? 'selected' : '' }}>Dosen</option>
            </select>
        </div>
        <div class="mb-3"><label>Nomor HP</label><input type="text" name="nomor_hp" value="{{ $user->nomor_hp }}" class="form-control" required></div>
        <div class="mb-3"><label>Password (Kosongkan jika tidak ganti)</label><input type="password" name="password" class="form-control"></div>
        <div class="d-flex justify-content-between">
            <a href="/users" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Update Data</button>
        </div>
    </form>
</div>
@endsection
