<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExcelFile;

class InputDataController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jenis_data'     => 'required|string',
            'tahun'          => 'required|integer',

            'nama_sheet'     => 'required|string|max:255',
            'id_spreadsheet' => 'required|string|max:255',
            'jumlah_sheet'   => 'required|integer',
            'total_baris'    => 'required|integer',

            'file_excel'     => 'required|file|mimes:xls,xlsx|max:10240',
        ]);

        $file = $request->file('file_excel');

        // Nama file
        $namaFile = time() . '_' . $file->getClientOriginalName();

        // Folder tujuan
        $folder = public_path('uploads');

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // Upload file
        $file->move($folder, $namaFile);

        // Simpan ke database
        ExcelFile::create([
            'jenis_data'     => $request->jenis_data,
            'tahun'          => $request->tahun,
            'nama_sheet'     => $request->nama_sheet,
            'id_spreadsheet' => $request->id_spreadsheet,
            'jumlah_sheet'   => $request->jumlah_sheet,
            'total_baris'    => $request->total_baris,
            'nama_file'      => $namaFile,
            'file_path'      => 'uploads/' . $namaFile,
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
}