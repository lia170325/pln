<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khs extends Model
{
    use HasFactory;

    protected $table = 'khs'; // Sesuaikan dengan nama tabel di database kamu

    protected $fillable = [
        'nama_proyek',
        'gambar',
        'kontrak_pdf',
        'arcgis_link'
    ];
}