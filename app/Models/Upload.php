<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'uploads';

    public $timestamps = false;

    protected $fillable = [
        'nama_files',
        'file_path',
        'versi',
        'jumlah_sheet',
        'upload_by',
        'created_at',
        'status',
    ];
}