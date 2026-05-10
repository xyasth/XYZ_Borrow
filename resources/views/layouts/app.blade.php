<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: #212529;
            color: white;
            padding: 20px;
        }

        .nav-link {
            color: #adb5bd;
        }

        .nav-link.active,
        .nav-link:hover {
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <nav class="col-md-2 d-none d-md-block sidebar d-flex flex-column justify-content-between">
                <div>
                    <h4 class="text-center mb-4">Univ XYZ</h4>
                    <div class="text-center mb-3">
                        <small class="text-muted">Halo, {{ Auth::user()->nama }}</small><br>
                        <span class="badge bg-secondary">{{ strtoupper(Auth::user()->jenis_akun) }}</span>
                    </div>
                    <ul class="nav flex-column">
                        @if (Auth::user()->jenis_akun === 'admin')
                            <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link"><i
                                        class="bi bi-clipboard-check me-2"></i> Semua Peminjaman</a></li>
                            <li class="nav-item"><a href="{{ route('users.index') }}" class="nav-link"><i
                                        class="bi bi-people me-2"></i> Peminjam (User)</a></li>
                            <li class="nav-item"><a href="{{ route('ruang.index') }}" class="nav-link"><i
                                        class="bi bi-door-open me-2"></i> Ruang</a></li>
                            <li class="nav-item"><a href="{{ route('peralatan.index') }}" class="nav-link"><i
                                        class="bi bi-tools me-2"></i> Peralatan</a></li>
                            <li class="nav-item mt-4"><a href="{{ route('reports.index') }}"
                                    class="nav-link text-info"><i class="bi bi-file-earmark-pdf me-2"></i> Laporan</a>
                            </li>
                        @else
                            <li class="nav-item"><a href="{{ route('user.dashboard') }}" class="nav-link"><i
                                        class="bi bi-house me-2"></i> Dashboard Saya</a></li>
                            <li class="nav-item"><a href="{{ route('peminjaman.create') }}" class="nav-link"><i
                                        class="bi bi-plus-circle me-2"></i> Buat Pengajuan</a></li>
                        @endif
                    </ul>
                </div>
                <div class="mt-5 p-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light w-100"><i
                                class="bi bi-box-arrow-right me-2"></i> Logout</button>
                    </form>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
