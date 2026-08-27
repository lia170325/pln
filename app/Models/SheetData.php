<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SheetData extends Model
{
    protected $table = 'sheet_data';

    public $timestamps = false;

    protected $fillable = [
        'sheet_id',
        'row_number',
        'row_data',
        'created_at',
    ];
}