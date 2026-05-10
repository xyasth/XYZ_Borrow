@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Peralatan</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAlatModal">Tambah Alat</button>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Stok Tersedia</th>
                        <th>Total Aset</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peralatan as $p)
                        <tr>
                            <td><code>{{ $p->kode_alat }}</code></td>
                            <td>{{ $p->nama_alat }}</td>
                            <td>{{ $p->kategori }}</td>
                            <td><span class="fw-bold {{ $p->stok < 3 ? 'text-danger' : '' }}">{{ $p->stok }}</span></td>
                            <td>{{ $p->total_aset }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('peralatan.edit', $p->peralatan_id) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <form action="{{ route('peralatan.destroy', $p->peralatan_id) }}" method="POST"
                                        class="d-inline ms-1">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus alat ini?')">
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

    <div class="modal fade" id="addAlatModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('peralatan.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5>Tambah Peralatan</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Kode Alat</label><input type="text" name="kode_alat" class="form-control"
                            placeholder="MISAL: PRJ-001" required></div>
                    <div class="mb-3"><label>Nama Alat</label><input type="text" name="nama_alat" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Kategori</label><input type="text" name="kategori" class="form-control"
                            placeholder="Elektronik/Media" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Stok Saat Ini</label><input type="number" name="stok"
                                class="form-control" min="0" required></div>
                        <div class="col-md-6 mb-3"><label>Total Aset</label><input type="number" name="total_aset"
                                class="form-control" min="1" required></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan Alat</button></div>
            </form>
        </div>
    </div>
@endsection
