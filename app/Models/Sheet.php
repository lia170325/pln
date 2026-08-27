<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    protected $table = 'sheet';

    public $timestamps = false;

    protected $fillable = [

    'upload_id',
    'nama_sheet',
    'kategori',
    'jenis_monitoring', // TAMBAHAN: khs / tiang / pelanggan
    'tahun',
    'urutan',
    'total_rows',

    'merge_cells',
    'highest_row',
    'highest_column',

    'is_active',
    'created_at'

];
}