<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman #{{ $peminjaman->peminjaman_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0 0 0; font-size: 14px; }

        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-info td {
            padding: 6px;
            vertical-align: top;
        }
        .table-info td:first-child {
            width: 30%;
            font-weight: bold;
        }

        .table-alat {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-alat th, .table-alat td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table-alat th {
            background-color: #f2f2f2;
        }

        /* Styling Status */
        .status-badge {
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { color: #d39e00; }
        .status-approved { color: #0056b3; }
        .status-rejected { color: #dc3545; }
        .status-finished { color: #28a745; }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-style: italic;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>BUKTI TRANSAKSI PEMINJAMAN</h2>
        <p>Universitas XYZ</p>
    </div>

    <table class="table-info">
        <tr>
            <td>Nomor Transaksi</td>
            <td>: #{{ str_pad($peminjaman->peminjaman_id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td>Status Transaksi</td>
            <td>: <span class="status-badge status-{{ $peminjaman->status }}">{{ $peminjaman->status }}</span></td>
        </tr>
        <tr>
            <td>Identitas Peminjam</td>
            <td>: {{ $peminjaman->user->nama }} ({{ $peminjaman->user->nomor_induk }}) - {{ strtoupper($peminjaman->user->jenis_akun) }}</td>
        </tr>
        <tr>
            <td>Ruangan Dipinjam</td>
            <td>: {{ $peminjaman->ruang ? $peminjaman->ruang->nama . ' (Lantai ' . $peminjaman->ruang->lantai . ')' : 'Tidak meminjam ruang' }}</td>
        </tr>
        <tr>
            <td>Jadwal Penggunaan</td>
            <td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_penggunaan)->format('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td>Durasi</td>
            <td>: {{ $peminjaman->durasi }} Jam</td>
        </tr>
        @if($peminjaman->waktu_kembali)
        <tr>
            <td>Waktu Dikembalikan</td>
            <td>: {{ \Carbon\Carbon::parse($peminjaman->waktu_kembali)->format('d F Y, H:i') }} WIB</td>
        </tr>
        @endif
        <tr>
            <td>Keterangan / Acara</td>
            <td>: {{ $peminjaman->keterangan }}</td>
        </tr>
        <tr>
            <td>Catatan Admin</td>
            <td>: {{ $peminjaman->catatan_admin ?? '-' }}</td>
        </tr>
    </table>

    <h3>Detail Peralatan</h3>
    <table class="table-alat">
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 25%;">Kode Alat</th>
                <th style="width: 45%;">Nama Peralatan</th>
                <th style="width: 20%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman->detailAlat as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->peralatan->kode_alat }}</td>
                <td>{{ $detail->peralatan->nama_alat }}</td>
                <td>{{ $detail->jumlah_pinjam }} Unit</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic;">Hanya meminjam ruang, tidak ada peralatan yang dipinjam.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Universitas XYZ pada {{ now()->format('d F Y, H:i:s') }} WIB
    </div>

</body>
</html>
