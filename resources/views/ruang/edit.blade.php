@extends('layouts.app')

@section('content')
<div class="card shadow-sm mx-auto" style="max-width: 700px;">
    <div class="card-header bg-warning py-3">
        <h5 class="mb-0 text-dark">Edit Data Ruang: {{ $ruang->nama }}</h5>
    </div>
    <form action="{{ route('ruang.update', $ruang->ruang_id) }}" method="POST" class="card-body">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-bold">Nama Ruang</label>
            <input type="text" name="nama" value="{{ $ruang->nama }}" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kapasitas</label>
                <input type="number" name="kapasitas" value="{{ $ruang->kapasitas }}" class="form-control" min="1" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Lantai</label>
                <input type="text" name="lantai" value="{{ $ruang->lantai }}" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Gedung</label>
                <select name="gedung" class="form-select">
                    <option value="gedung_a" {{ $ruang->gedung == 'gedung_a' ? 'selected' : '' }}>Gedung A</option>
                    <option value="gedung_b" {{ $ruang->gedung == 'gedung_b' ? 'selected' : '' }}>Gedung B</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status Ketersediaan</label>
                <select name="status" class="form-select">
                    <option value="available" {{ $ruang->status == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ $ruang->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="maintenance" {{ $ruang->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <a href="/ruang" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
