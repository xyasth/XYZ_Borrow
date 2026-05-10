<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // database/migrations/2026_01_01_000003_create_peminjaman_table.php
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('peminjaman_id');
            // Foreign Keys
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('ruang_id')->nullable()->constrained('ruang', 'ruang_id')->onDelete('cascade');            // Attributes
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_penggunaan');
            $table->integer('durasi'); // dalam jam
            $table->enum('status', ['pending', 'approved', 'rejected', 'finished'])->default('pending');
            $table->dateTime('waktu_kembali')->nullable();
            $table->text('keterangan');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
