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
        // database/migrations/2026_01_01_000002_create_peralatan_table.php
        Schema::create('peralatan', function (Blueprint $table) {
            $table->id('peralatan_id');
            $table->string('kode_alat')->unique();
            $table->string('nama_alat');
            $table->integer('total_aset');
            $table->string('kategori');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};
