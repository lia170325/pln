<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelFile extends Model
{
    protected $table = 'excel_files';

    protected $fillable = [
        'jenis_data',
        'tahun',
        'nama_sheet',
        'id_spreadsheet',
        'jumlah_sheet',
        'total_baris',
        'nama_file',
        'file_path',
    ];
}