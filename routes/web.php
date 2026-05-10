<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\PeralatanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/api/check-availability', [PeminjamanController::class, 'checkAvailability'])->name('api.check');
    // KHUSUS ADMIN
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [PeminjamanController::class, 'index'])->name('admin.dashboard');

        Route::resource('users', UserController::class);
        Route::resource('ruang', RuangController::class);
        Route::resource('peralatan', PeralatanController::class);
        Route::patch('/peminjaman/{id}/status', [PeminjamanController::class, 'updateStatus'])->name('peminjaman.updateStatus');

        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/rekap-pdf', [ReportController::class, 'exportRekap'])->name('reports.pdf');
        Route::get('/peminjaman/{id}/pdf', [ReportController::class, 'exportPdf'])->name('peminjaman.pdf');
    });

    // KHUSUS PEMINJAM (Mahasiswa & Dosen)
    Route::middleware(['role:mahasiswa,dosen'])->group(function () {
        Route::get('/dashboard', [PeminjamanController::class, 'riwayat'])->name('user.dashboard');

        // PERBAIKAN: Menambahkan penamaan rute di bawah ini
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });
});
