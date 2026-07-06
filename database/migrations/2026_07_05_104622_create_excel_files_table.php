<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_files', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sheet');
            $table->string('id_spreadsheet');
            $table->integer('jumlah_sheet');
            $table->integer('total_baris');
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('jenis_data');
            $table->year('tahun');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_files');
    }
};