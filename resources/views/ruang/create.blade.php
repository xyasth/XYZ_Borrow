@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ruang">Manajemen Ruang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Ruang</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Data Ruang Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('ruang.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama" class="form-label fw-bold">Nama Ruang / Kelas</label>
                                <input type="text" name="nama" id="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       placeholder="Contoh: Lab Komputer 1 atau Aula Gedung A"
                                       value="{{ old('nama') }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="kapasitas" class="form-label fw-bold">Kapasitas (Orang)</label>
                                <div class="input-group">
                                    <input type="number" name="kapasitas" id="kapasitas"
                                           class="form-control @error('kapasitas') is-invalid @enderror"
                                           min="1" placeholder="0"
                                           value="{{ old('kapasitas') }}" required>
                                    <span class="input-group-text">Orang</span>
                                </div>
                                <small class="text-muted">Minimal 1 orang.</small>
                                @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lantai" class="form-label fw-bold">Lantai</label>
                                <input type="text" name="lantai" id="lantai"
                                       class="form-control @error('lantai') is-invalid @enderror"
                                       placeholder="Contoh: 1, 2, atau Dasar"
                                       value="{{ old('lantai') }}" required>
                                @error('lantai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gedung" class="form-label fw-bold">Gedung</label>
                                <select name="gedung" id="gedung" class="form-select @error('gedung') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Gedung --</option>
                                    <option value="gedung_a" {{ old('gedung') == 'gedung_a' ? 'selected' : '' }}>Gedung A</option>
                                    <option value="gedung_b" {{ old('gedung') == 'gedung_b' ? 'selected' : '' }}>Gedung B</option>
                                </select>
                                @error('gedung') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">Status Awal</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available (Siap Pakai)</option>
                                    <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="/ruang" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-save me-1"></i> Simpan Ruang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
