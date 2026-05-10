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
        // database/migrations/2026_01_01_000004_create_detail_peminjaman_alat_table.php
        Schema::create('detail_peminjaman_alat', function (Blueprint $table) {
            $table->id('peminjaman_peralatan_id');
            // Foreign Keys
            $table->foreignId('peminjaman_id')->constrained('peminjaman', 'peminjaman_id')->onDelete('cascade');
            $table->foreignId('peralatan_id')->constrained('peralatan', 'peralatan_id')->onDelete('cascade');

            $table->integer('jumlah_pinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman_alats');
    }
};
