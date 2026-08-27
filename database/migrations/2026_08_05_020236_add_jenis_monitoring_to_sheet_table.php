<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==========================
// TAMBAHAN: Kolom penanda jenis monitoring (khs / tiang / pelanggan)
// pada tabel "sheet".
//
// Default 'khs' supaya SELURUH data lama yang sudah ada otomatis
// tetap masuk ke Monitoring KHS tanpa perlu migrasi data manual,
// dan fitur Monitoring KHS yang sudah berjalan tidak terganggu sama
// sekali.
// ==========================
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sheet', function (Blueprint $table) {
            $table->string('jenis_monitoring', 30)
                ->default('khs')
                ->after('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('sheet', function (Blueprint $table) {
            $table->dropColumn('jenis_monitoring');
        });
    }
};