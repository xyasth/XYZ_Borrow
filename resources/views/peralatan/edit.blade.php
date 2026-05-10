@extends('layouts.app')

@section('content')
<div class="card shadow-sm mx-auto" style="max-width: 700px;">
    <div class="card-header bg-warning py-3">
        <h5 class="mb-0 text-dark">Edit Peralatan: {{ $alat->nama_alat }}</h5>
    </div>
    <form action="{{ route('peralatan.update', $alat->peralatan_id) }}" method="POST" class="card-body">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-bold">Kode Alat</label>
            <input type="text" name="kode_alat" value="{{ $alat->kode_alat }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nama Peralatan</label>
            <input type="text" name="nama_alat" value="{{ $alat->nama_alat }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Kategori</label>
            <input type="text" name="kategori" value="{{ $alat->kategori }}" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Stok Saat Ini</label>
                <input type="number" name="stok" value="{{ $alat->stok }}" class="form-control" min="0" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Total Aset</label>
                <input type="number" name="total_aset" value="{{ $alat->total_aset }}" class="form-control" min="1" required>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <a href="/peralatan" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Update Data Alat</button>
        </div>
    </form>
</div>
@endsection
