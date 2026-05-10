@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manajemen Peminjam</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah Peminjam</button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>NIM/NIK</th>
                    <th>Email</th>
                    <th>Akun</th>
                    <th>No. HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->nama }}</td>
                    <td>{{ $user->nomor_induk }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($user->jenis_akun) }}</span></td>
                    <td>{{ $user->nomor_hp }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5>Tambah Peminjam Baru</h5></div>
            <div class="modal-body">
                <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                <div class="mb-3"><label>NIM / NIK</label><input type="text" name="nomor_induk" class="form-control" required></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label>Jenis Akun</label>
                    <select name="jenis_akun" class="form-select">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>
                <div class="mb-3"><label>Nomor HP</label><input type="text" name="nomor_hp" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
    </div>
</div>
@endsection
