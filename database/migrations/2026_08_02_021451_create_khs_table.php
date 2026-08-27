<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('khs', function (Blueprint $table) {
        $table->id();
        $table->string('nama_proyek'); // Kolom untuk nama proyek
        $table->string('gambar')->nullable(); // Kolom untuk simpan nama file gambar
        $table->string('kontrak_pdf')->nullable(); // Kolom untuk simpan nama file PDF
        $table->string('arcgis_link')->nullable(); // Kolom untuk simpan link peta
        $table->timestamps(); // Bawaan laravel (created_at & updated_at)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khs');
    }
};
