<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Sheet;
use App\Models\SheetData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class InputDataController extends Controller
{
    // ==========================
    // TAMBAHAN: Tiga pintu masuk upload terpisah sesuai jenis monitoring.
    // Semuanya memanggil logika import yang SAMA (importExcel), supaya
    // proses baca Excel & simpan ke database tetap satu sumber kebenaran
    // (tidak dobel kode, lebih gampang dirawat) tapi tetap terpisah
    // datanya per jenis monitoring.
    // ==========================

    public function storeKhs(Request $request)
    {
        return $this->importExcel($request, 'khs', 'Monitoring KHS');
    }

    public function storeTiang(Request $request)
    {
        return $this->importExcel($request, 'tiang', 'Monitoring Tiang');
    }

    public function storePelanggan(Request $request)
    {
        return $this->importExcel($request, 'pelanggan', 'Monitoring Pelanggan');
    }

    // Dipertahankan supaya route/kode lama yang masih memanggil store()
    // langsung tetap berfungsi seperti sebelumnya (upload KHS).
    public function store(Request $request)
    {
        return $this->storeKhs($request);
    }

    // ==========================
    // Proses import Excel (dipakai bersama oleh KHS/Tiang/Pelanggan)
    // ==========================
    private function importExcel(Request $request, string $jenisMonitoring, string $labelMonitoring)
    {
        // ===========================
        // Naikkan batas memori & waktu eksekusi
        // khusus untuk request ini saja.
        // File Excel dengan banyak sheet/formula
        // butuh memori lebih dari default 512M
        // agar tidak fatal error saat load/kalkulasi.
        // ===========================
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:20480'
        ]);

        DB::beginTransaction();

        try {
            // ===========================
            // Upload File
            // ===========================
            $file = $request->file('file_excel');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $namaFile, 'public');

            $fullPath = storage_path('app/public/' . $path);

            // ===========================
            // Baca Excel
            // ===========================
            // PERBAIKAN: Sebelumnya dipakai setReadDataOnly(true) dengan
            // tujuan supaya PhpSpreadsheet tidak menyentuh mesin formula.
            // Setelah ditelusuri ke source PhpSpreadsheet
            // (Reader/Xlsx.php), setReadDataOnly(true) TERNYATA tidak ada
            // hubungannya dengan formula sama sekali - opsi ini membuat
            // reader melewati (skip) SEMUA info STYLE per sel (termasuk
            // Number Format tanggal/angka/Rp) dan info MERGE CELL, karena
            // baris `if ($cAttr['s'] && !$this->readDataOnly)` dan
            // `... && !$this->readDataOnly` pada mergeCells tidak pernah
            // dijalankan saat readDataOnly = true. Akibatnya SETIAP sel
            // dianggap berformat "General", sehingga tanggal tampil
            // sebagai angka serial Excel (mis. 46358) dan format angka
            // (Rp, ribuan, dst) ikut hilang.
            //
            // Baris `setReadDataOnly(true)` DIHAPUS supaya style (Number
            // Format) ikut terbaca dan tanggal/angka tampil sesuai format
            // Excel aslinya. Keamanan terhadap error formula TETAP terjaga
            // seperti sebelumnya karena readWorksheetToArray() di bawah
            // memang sudah memakai getOldCalculatedValue() (nilai cache
            // hasil terakhir dari Excel) untuk sel formula, BUKAN memicu
            // mesin kalkulasi PhpSpreadsheet - jadi tidak berkaitan dengan
            // opsi readDataOnly ini.
            $reader = IOFactory::createReaderForFile($fullPath);
            $spreadsheet = $reader->load($fullPath);

            $sheetCount = $spreadsheet->getSheetCount();

            // ===========================
            // Simpan Upload
            // ===========================
            $upload = Upload::create([
                'nama_files'   => $namaFile,
                'file_path'    => $path,
                'versi'        => 'v1',
                'jumlah_sheet' => $sheetCount,
                'upload_by'    => 1,
                'created_at'   => now(),
                'status'       => 'aktif'
            ]);

            // ===========================
            // Hapus Data Sheet & SheetData Lama
            // TAMBAHAN: HANYA untuk jenis monitoring yang sama.
            // Supaya upload Monitoring Tiang/Pelanggan tidak ikut
            // menghapus data Monitoring KHS (atau sebaliknya).
            // Riwayat di tabel "uploads" tetap disimpan.
            // ===========================
            $oldSheetIds = Sheet::where('jenis_monitoring', $jenisMonitoring)->pluck('id');
            SheetData::whereIn('sheet_id', $oldSheetIds)->delete();
            Sheet::where('jenis_monitoring', $jenisMonitoring)->delete();

            // ===========================
            // Loop Semua Sheet
            // ===========================
            foreach ($spreadsheet->getWorksheetIterator() as $index => $worksheet) {

                $sheetName = trim($worksheet->getTitle());
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex =
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // TAMBAHAN/PERBAIKAN: Sebelumnya dipakai
                // $worksheet->toArray(null, false, true, false) - parameter
                // ke-2 (false) adalah $calculateFormulas. Di dalam
                // PhpSpreadsheet (Worksheet::cellToArray()), kalau
                // $calculateFormulas = false, maka untuk SEL BERFORMULA
                // yang dipakai adalah $cell->getValue() - yaitu TEKS
                // FORMULA MENTAH (mis. "='KHS JASA 2026'!B2"), BUKAN hasil
                // hitungannya. Ini penyebab bug "yang tampil di website
                // adalah tulisan formula, bukan hasilnya".
                //
                // FIX: dipakai fungsi readWorksheetToArray() di bawah, yang
                // untuk sel berformula mengambil getOldCalculatedValue() -
                // yaitu NILAI HASIL TERAKHIR yang sudah dihitung & disimpan
                // sendiri oleh Excel di dalam file (persis "nilai hasil
                // terakhir yang sudah tersimpan di file Excel" seperti
                // tujuan awal kode ini) TANPA menjalankan mesin kalkulasi
                // formula PhpSpreadsheet sama sekali - jadi tetap aman dari
                // error "Unexpected operator" dkk pada formula kompleks.
                $rows = $this->readWorksheetToArray($worksheet, $highestRow, $highestColumnIndex);

                // ===========================
                // TAMBAHAN: Tangkap URL dari Hyperlink Excel
                // Kalau di Excel ada sel yang tampil sebagai teks biasa
                // (mis. "Lihat Lokasi", "Buka Dokumen") tapi sebenarnya
                // di-klik akan membuka link (dipasang lewat fitur
                // "Insert Link" di Excel), maka URL aslinya TIDAK ikut
                // terbaca oleh toArray() di atas (yang terbaca cuma teks
                // tampilannya). Blok ini mengambil URL asli dari metadata
                // Hyperlink Excel dan menaruhnya menggantikan teks
                // tampilan, KHUSUS untuk sel yang isinya belum berupa URL
                // mentah, supaya website tetap bisa membuka link tsb.
                // ===========================
                foreach ($worksheet->getHyperlinkCollection() as $coordinate => $hyperlink) {
                    $url = trim((string) $hyperlink->getUrl());

                    if ($url === '' || str_starts_with($url, '#')) {
                        continue; // lewati link internal antar-sel/sheet (bukan URL eksternal)
                    }

                    [$colLetter, $rowNumber] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($coordinate);
                    $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colLetter) - 1;
                    $rowIndex = $rowNumber - 1;

                    $currentValue = trim((string) ($rows[$rowIndex][$colIndex] ?? ''));

                    if (!preg_match('#^https?://#i', $currentValue)) {
                        $rows[$rowIndex][$colIndex] = $url;
                    }
                }

                // ===========================
                // Ambil Tahun
                // ===========================
                $tahun = null;

                if (preg_match('/(20\d{2})/', $sheetName, $match)) {
                    $tahun = $match[1];
                }

                // ===========================
                // Ambil Kategori
                // ===========================
                $kategori = $sheetName;

                if ($tahun) {
                    $kategori = trim(str_replace($tahun, '', $sheetName));
                }

                // ===========================
                // Simpan Sheet
                // ===========================
                $sheetModel = Sheet::create([
                    'upload_id'        => $upload->id,
                    'nama_sheet'       => $sheetName,
                    'kategori'         => strtoupper($kategori),
                    'jenis_monitoring' => $jenisMonitoring, // TAMBAHAN
                    'tahun'            => $tahun,
                    'urutan'           => $index + 1,
                    'total_rows'       => count($rows),
                    'is_active'        => 1,
                    'created_at'       => now(),
                ]);

                // ===========================
                // Simpan Isi Sheet (bulk insert)
                // ===========================
                $now = now();
                $bulkRows = [];

                foreach ($rows as $rowNumber => $row) {
                    $bulkRows[] = [
                        'sheet_id'   => $sheetModel->id,
                        'row_number' => $rowNumber + 1,
                        'row_data'   => json_encode($row),
                        'created_at' => $now,
                    ];
                }

                foreach (array_chunk($bulkRows, 500) as $chunk) {
                    SheetData::insert($chunk);
                }
            }

            DB::commit();

            // TAMBAHAN: kalau request ini datang dari progress bar upload
            // (AJAX/fetch, mengirim header Accept: application/json),
            // balas dengan JSON supaya JS bisa menampilkan status
            // "berhasil" tanpa reload halaman. Form submit BIASA (bukan
            // AJAX) TETAP memakai redirect + flash session persis seperti
            // sebelumnya - tidak ada perubahan untuk jalur lama itu.
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upload ' . $labelMonitoring . ' berhasil. Total Sheet : ' . $sheetCount,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Upload ' . $labelMonitoring . ' berhasil. Total Sheet : ' . $sheetCount);

        } catch (\Exception $e) {

            DB::rollBack();

            // TAMBAHAN: sama seperti di atas - balas JSON hanya untuk
            // request AJAX dari progress bar upload, supaya progress bar
            // bisa berhenti dan menampilkan pesan error yang sesuai.
            // Jalur form submit biasa TETAP balik ke back()->withErrors()
            // seperti sebelumnya.
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    // ==========================
    // TAMBAHAN/PERBAIKAN: Pengganti $worksheet->toArray() bawaan
    // PhpSpreadsheet, khusus supaya SEL BERFORMULA menampilkan HASIL
    // formulanya, bukan teks formula mentah.
    //
    // Cara kerjanya mengikuti persis logika internal
    // Worksheet::cellToArray() di PhpSpreadsheet (baca sel per sel,
    // ambil RichText sebagai teks polos, lalu terapkan format angka
    // bawaan sel) - HANYA BEDA di satu titik: untuk sel berformula,
    // dipakai getOldCalculatedValue() (nilai hasil terakhir yang sudah
    // dihitung & disimpan sendiri oleh Excel di file-nya), BUKAN
    // getCalculatedValue() (yang akan menjalankan ulang mesin formula
    // PhpSpreadsheet dan berisiko error pada formula kompleks/fungsi
    // yang tidak didukung) dan BUKAN pula getValue() (yang mengembalikan
    // teks formula mentah, ini penyebab bug sebelumnya).
    //
    // Hasil array yang dikembalikan strukturnya SAMA PERSIS dengan
    // toArray(null, ..., ..., false) sebelumnya: array baris (index 0..n)
    // berisi array kolom (index 0..m), supaya seluruh kode setelahnya
    // (hitung total_rows, simpan row_data, deteksi Hyperlink, dst) tidak
    // perlu diubah sama sekali.
    // ==========================
    private function readWorksheetToArray(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet,
        int $highestRow,
        int $highestColumnIndex
    ): array {
        $colLetters = [];
        for ($c = 1; $c <= $highestColumnIndex; $c++) {
            $colLetters[$c] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        }

        $rows = [];

        for ($r = 1; $r <= $highestRow; $r++) {
            $rowData = [];

            for ($c = 1; $c <= $highestColumnIndex; $c++) {
                // createIfNotExists = false, supaya sel kosong tidak ikut
                // dibuat di memori (sama seperti perilaku toArray() bawaan).
                $cell = $worksheet->getCell($colLetters[$c] . $r, false);

                if ($cell === null || $cell->getValue() === null) {
                    $rowData[] = null;
                    continue;
                }

                $rawValue = $cell->getValue();

                if ($rawValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                    $value = $rawValue->getPlainText();
                } elseif ($cell->isFormula()) {
                    // FIX UTAMA: pakai nilai hasil terakhir yang sudah
                    // dihitung & disimpan Excel sendiri.
                    $value = $cell->getOldCalculatedValue();

                    // Fallback jaga-jaga: hanya kepakai kalau file Excel
                    // ternyata belum pernah menyimpan nilai cache-nya sama
                    // sekali (jarang terjadi). Dibungkus try/catch supaya
                    // formula rumit/tidak didukung tidak menggagalkan
                    // seluruh proses import - kalau tetap gagal dihitung,
                    // baru dipakai teks formula apa adanya.
                    if ($value === null) {
                        try {
                            $value = $cell->getCalculatedValue();
                        } catch (\Throwable $e) {
                            $value = $rawValue;
                        }
                    }
                } else {
                    $value = $rawValue;
                }

                // Samakan dengan perilaku toArray() sebelumnya
                // ($formatData = true): terapkan format angka bawaan sel
                // (mis. General/Currency/Percent) ke nilai akhirnya.
                $style = $worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex());
                $value = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::toFormattedString(
                    $value,
                    $style->getNumberFormat()->getFormatCode() ?? \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL
                );

                $rowData[] = $value;
            }

            $rows[] = $rowData;
        }

        return $rows;
    }
}