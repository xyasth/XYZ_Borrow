@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Peminjaman</h2>
        @if (Auth::user()->jenis_akun === 'admin')
        @else
            <a href="/peminjaman/create" class="btn btn-primary">Buat Pengajuan Baru</a>
        @endif
    </div>

    <div class="card shadow-sm border-0 mb-5">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peminjam</th>
                        <th>Ruang</th>
                        <th>Waktu Pakai</th>
                        <th>Status</th>
                        <th>Aksi Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peminjaman as $p)
                        <tr>
                            <td><strong>{{ $p->user->nama }}</strong><br><small>{{ strtoupper($p->user->jenis_akun) }}</small></td>
                            <td>{{ $p->ruang ? $p->ruang->nama : 'Hanya Alat' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($p->tanggal_penggunaan)->format('d M Y, H:i') }}<br>
                                <small>{{ $p->durasi }} Jam</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $p->status == 'approved' ? 'primary' : ($p->status == 'pending' ? 'warning' : ($p->status == 'finished' ? 'success' : 'danger')) }}">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if ($p->status == 'pending')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#statusModal"
                                            onclick="window.openStatusModal('{{ route('peminjaman.updateStatus', $p->peminjaman_id) }}', 'approved', 'Konfirmasi Persetujuan', 'Tulis pesan atau instruksi tambahan (opsional)...', 'btn-success')">
                                            Setujui
                                        </button>

                                        <button type="button" class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal"
                                            data-bs-target="#statusModal"
                                            onclick="window.openStatusModal('{{ route('peminjaman.updateStatus', $p->peminjaman_id) }}', 'rejected', 'Alasan Penolakan', 'Wajib isi alasan penolakan agar peminjam tahu...', 'btn-danger')">
                                            Tolak
                                        </button>
                                    @elseif($p->status == 'approved')
                                        <form action="{{ route('peminjaman.updateStatus', $p->peminjaman_id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="finished">
                                            <button class="btn btn-sm btn-outline-primary">Selesaikan</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Selesai/Ditolak</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="statusForm" method="POST" class="modal-content">
                @csrf @method('PATCH')
                <input type="hidden" name="status" id="modal_status_input">

                <div class="modal-header" id="modal_header_bg">
                    <h5 class="modal-title text-white" id="modal_title_text">Update Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Admin</label>
                        <textarea name="catatan_admin" id="modal_textarea" class="form-control" rows="4"></textarea>
                        <div id="reject_hint" class="text-danger small mt-1 d-none">Catatan wajib diisi untuk penolakan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="modal_submit_btn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Deklarasi fungsi global agar bisa dibaca oleh tombol di dalam tabel
        window.openStatusModal = function(url, status, title, placeholder, btnClass) {
            // Set URL Aksi Form
            const form = document.getElementById('statusForm');
            form.action = url;

            // Set Value Status (approved/rejected)
            document.getElementById('modal_status_input').value = status;

            // Update Visual Modal
            document.getElementById('modal_title_text').innerText = title;
            document.getElementById('modal_textarea').placeholder = placeholder;

            // Atur warna header dan tombol
            const header = document.getElementById('modal_header_bg');
            const submitBtn = document.getElementById('modal_submit_btn');
            const hint = document.getElementById('reject_hint');
            const textarea = document.getElementById('modal_textarea');

            // Reset class warna
            header.className = 'modal-header ' + (status === 'approved' ? 'bg-success' : 'bg-danger');
            submitBtn.className = 'btn ' + btnClass;

            // Atur kewajiban isi (required) jika status rejected
            if (status === 'rejected') {
                textarea.setAttribute('required', 'required');
                hint.classList.remove('d-none');
            } else {
                textarea.removeAttribute('required');
                hint.classList.add('d-none');
            }
        };
    </script>
@endsection
