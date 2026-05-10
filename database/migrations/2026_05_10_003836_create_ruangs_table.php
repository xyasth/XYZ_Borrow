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
        // database/migrations/2026_01_01_000001_create_ruang_table.php
        Schema::create('ruang', function (Blueprint $table) {
            $table->id('ruang_id');
            $table->string('nama');
            $table->integer('kapasitas');
            $table->string('lantai');
            $table->enum('gedung', ['gedung_a', 'gedung_b']); // Sesuai enumeration ListGedung
            $table->enum('status', ['available', 'unavailable', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangs');
    }
};
