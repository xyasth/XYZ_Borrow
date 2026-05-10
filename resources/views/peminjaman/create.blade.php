@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Buat Pengajuan Peminjaman Baru</h5>
        </div>
        <form action="{{ route('peminjaman.store') }}" method="POST" class="card-body p-4">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">Peminjam</label>
                <input type="text" class="form-control bg-light" value="{{ Auth::user()->nama }} ({{ strtoupper(Auth::user()->jenis_akun) }})" readonly>
            </div>

            <div class="row bg-light p-3 rounded mb-4 border">
                <h6 class="text-secondary mb-3">Tentukan Jadwal Dahulu</h6>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold text-danger">1. Tanggal Pakai *</label>
                    <input type="date" name="tanggal" id="input_tanggal" class="form-control border-danger" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold text-danger">2. Jam Mulai *</label>
                    <select name="jam_mulai" id="input_mulai" class="form-select border-danger" required>
                        <option value="">-- Pilih Jam --</option>
                        @for($i = 1; $i <= 24; $i++) <option value="{{ $i }}">{{ sprintf('%02d', $i) }}:00</option> @endfor
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold text-danger">3. Jam Selesai *</label>
                    <select name="jam_selesai" id="input_selesai" class="form-select border-danger" required>
                        <option value="">-- Pilih Jam --</option>
                        @for($i = 1; $i <= 24; $i++) <option value="{{ $i }}">{{ sprintf('%02d', $i) }}:00</option> @endfor
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Pilih Ruangan</label>
                <select name="ruang_id" id="select_ruang" class="form-select bg-light" disabled>
                    <option value="">-- Lengkapi jadwal di atas terlebih dahulu --</option>
                </select>
                <small class="text-muted">Biarkan kosong jika hanya meminjam peralatan.</small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Peralatan (Opsional)</label>
                <div id="peralatan_container">
                    <div class="row mb-2 alat-row">
                        <div class="col-md-8">
                            <select name="peralatan[0][id]" class="form-select select-alat bg-light" disabled>
                                <option value="">-- Lengkapi jadwal di atas terlebih dahulu --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="peralatan[0][jumlah]" class="form-control" placeholder="Jumlah" min="1">
                        </div>
                    </div>
                </div>
                <button type="button" id="btn_add_alat" class="btn btn-sm btn-outline-secondary mt-2 d-none">+ Tambah Peralatan Lain</button>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Keterangan / Tujuan Acara</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Rapat Himpunan Mahasiswa" required></textarea>
            </div>

            <button type="submit" id="btn_submit" class="btn btn-primary px-5" disabled>Kirim Pengajuan</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const inputTgl = document.getElementById('input_tanggal');
    const inputMulai = document.getElementById('input_mulai');
    const inputSelesai = document.getElementById('input_selesai');
    const selectRuang = document.getElementById('select_ruang');
    const btnSubmit = document.getElementById('btn_submit');
    const btnAddAlat = document.getElementById('btn_add_alat');
    const containerAlat = document.getElementById('peralatan_container');

    let globalPeralatan = []; // Menyimpan data peralatan dari server
    let indexAlat = 1;

    function fetchKetersediaan() {
        const tgl = inputTgl.value;
        const mulai = parseInt(inputMulai.value);
        const selesai = parseInt(inputSelesai.value);

        // Reset state jika belum valid
        if (!tgl || isNaN(mulai) || isNaN(selesai) || selesai <= mulai) {
            selectRuang.innerHTML = '<option value="">-- Lengkapi jadwal dengan benar --</option>';
            selectRuang.disabled = true;
            selectRuang.classList.add('bg-light');
            btnSubmit.disabled = true;
            btnAddAlat.classList.add('d-none');
            resetAlatDropdown();
            return;
        }

        // Ambil data ke API
        fetch(`/api/check-availability?tanggal=${tgl}&jam_mulai=${mulai}&jam_selesai=${selesai}`)
            .then(res => res.json())
            .then(data => {
                // 1. Render Ruang
                selectRuang.innerHTML = '<option value="">-- Hanya Pinjam Peralatan --</option>';
                data.ruang.forEach(r => {
                    let opt = document.createElement('option');
                    opt.value = r.id;
                    opt.text = r.nama;
                    if (r.disabled) opt.disabled = true;
                    selectRuang.add(opt);
                });
                selectRuang.disabled = false;
                selectRuang.classList.remove('bg-light');

                // 2. Simpan Data Peralatan & Update Dropdown Pertama
                globalPeralatan = data.peralatan;
                updateAllAlatDropdowns();

                // Buka Kunci Submit
                btnSubmit.disabled = false;
                btnAddAlat.classList.remove('d-none');
            })
            .catch(err => {
                alert("Gagal menghubungi server. Periksa koneksi Anda.");
            });
    }

    function resetAlatDropdown() {
        document.querySelectorAll('.select-alat').forEach(sel => {
            sel.innerHTML = '<option value="">-- Lengkapi jadwal di atas terlebih dahulu --</option>';
            sel.disabled = true;
            sel.classList.add('bg-light');
        });
    }

    function updateAllAlatDropdowns() {
        document.querySelectorAll('.select-alat').forEach(sel => {
            const valSaatIni = sel.value;
            sel.innerHTML = '<option value="">-- Pilih Peralatan --</option>';

            globalPeralatan.forEach(p => {
                let opt = document.createElement('option');
                opt.value = p.id;
                opt.text = p.nama;
                if (p.sisa <= 0) opt.disabled = true;
                if (p.id == valSaatIni) opt.selected = true; // Pertahankan pilihan sebelumnya
                sel.add(opt);
            });
            sel.disabled = false;
            sel.classList.remove('bg-light');
        });
    }

    // Tambah Baris Peralatan
    btnAddAlat.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row mb-2 alat-row';
        row.innerHTML = `
            <div class="col-md-8">
                <select name="peralatan[${indexAlat}][id]" class="form-select select-alat">
                    <option value="">-- Pilih Peralatan --</option>
                </select>
            </div>
            <div class="col-md-4 d-flex">
                <input type="number" name="peralatan[${indexAlat}][jumlah]" class="form-control me-2" placeholder="Jumlah" min="1">
                <button type="button" class="btn btn-outline-danger btn-remove-alat"><i class="bi bi-trash"></i></button>
            </div>
        `;
        containerAlat.appendChild(row);
        indexAlat++;
        updateAllAlatDropdowns(); // Isi opsi dropdown yang baru dibuat
    });

    // Hapus Baris Peralatan
    containerAlat.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-alat')) {
            e.target.closest('.alat-row').remove();
        }
    });

    // Trigger Update
    inputTgl.addEventListener('change', fetchKetersediaan);
    inputMulai.addEventListener('change', fetchKetersediaan);
    inputSelesai.addEventListener('change', fetchKetersediaan);

</script>
@endpush
