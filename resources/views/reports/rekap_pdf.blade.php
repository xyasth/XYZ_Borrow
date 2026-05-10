<!DOCTYPE html>
<html>
<head>
    <title>Rekap Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>UNIVERSITAS XYZ</h2>
        <h3>Laporan Rekap Peminjaman Ruang & Alat</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Peminjam</th>
                <th>Ruang</th>
                <th>Tanggal Pakai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $p)
            <tr>
                <td>#{{ $p->peminjaman_id }}</td>
                <td>{{ $p->user->nama }}</td>
                <td>{{ $p->ruang->nama }}</td>
                <td>{{ $p->tanggal_penggunaan->format('d/m/Y H:i') }}</td>
                <td>{{ strtoupper($p->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
