@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Ruang</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Ruang</button>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Ruang</th>
                        <th>Kapasitas</th>
                        <th>Gedung/Lantai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ruang as $r)
                        <tr>
                            <td>{{ $r->nama }}</td>
                            <td>{{ $r->kapasitas }} Orang</td>
                            <td>{{ strtoupper(str_replace('_', ' ', $r->gedung)) }} / Lantai {{ $r->lantai }}</td>
                            <td>
                                <span
                                    class="badge {{ $r->status == 'available' ? 'bg-success' : ($r->status == 'maintenance' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('ruang.edit', $r->ruang_id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <form action="{{ route('ruang.destroy', $r->ruang_id) }}" method="POST"
                                        class="d-inline ms-1">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus ruang ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('ruang.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5>Tambah Ruang Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nama Ruang</label><input type="text" name="nama" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Kapasitas</label><input type="number" name="kapasitas" class="form-control"
                            min="1" required></div>
                    <div class="mb-3"><label>Gedung</label>
                        <select name="gedung" class="form-select">
                            <option value="gedung_a">Gedung A</option>
                            <option value="gedung_b">Gedung B</option>
                        </select>
                    </div>
                    <div class="mb-3"><label>Lantai</label><input type="text" name="lantai" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Status</label>
                        <select name="status" class="form-select">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
