<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Sheet;
use App\Models\SheetData;
use App\Models\Khs; // <-- TAMBAHAN: Panggil Model Khs di sini

class DashboardController extends Controller
{
    // ==========================
    // Dashboard User
    // ==========================
    public function userIndex()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // TAMBAHAN: Struktur Sidebar bertingkat (Nested Dropdown)
        $sidebarMenu = $this->buildSidebarMenu();

        // TAMBAHAN: Hitung ulang ringkasan kartu berdasarkan sheet yang
        // benar-benar tampil di Sidebar (BKJ & Sheet1 tidak dihitung)
        $totalSheet = 0;
        foreach ($sidebarMenu as $topGroup) {
            if (($topGroup['type'] ?? 'flat') === 'nested') {
                foreach ($topGroup['children'] as $sheetsInSub) {
                    $totalSheet += count($sheetsInSub);
                }
            } else {
                $totalSheet += count($topGroup['items']);
            }
        }
        $totalKategori = count($sidebarMenu);

        // TAMBAHAN: Data Grafik & Ringkasan Dashboard (diambil dari sheet "KHS Jasa 2026")
        $khsChart = $this->getKhsJasaChartData();

        return view('dashboard', compact('sidebarMenu', 'khsChart', 'totalSheet', 'totalKategori'));
    }

    // ==========================
    // TAMBAHAN: Bangun struktur menu Sidebar (Nested Dropdown)
    // Hanya mengubah tampilan/pengelompokan, TIDAK mengubah data di database.
    // Pengelompokan otomatis berdasarkan pola nama/kategori sheet:
    // - Kategori diawali "RES"   -> masuk grup "Reservasi" (flat)
    // - Kategori diawali "REKAP" -> masuk grup "Rekap" (flat)
    // - Kategori diawali "KHS"   -> masuk grup "KHS", dikelompokkan lagi
    //   per sub-kategori (mis. "KHS Jasa", "KHS Cover")
    // - Kategori/nama sheet "BKJ" atau "Sheet1" -> disembunyikan
    // - Kategori lain di luar itu -> tetap ditampilkan apa adanya
    //   sebagai grup tersendiri, supaya tidak ada data yang hilang
    //   dari Sidebar meskipun belum termasuk pola di atas.
    // ==========================
    private function buildSidebarMenu()
    {
        $sheets = Sheet::orderBy('kategori')
            ->orderBy('tahun')
            ->orderBy('urutan')
            ->get();

        $menu = [];

        // TAMBAHAN: Kategori yang harus ikut digabung ke dalam kelompok
        // KHS walau namanya tidak diawali kata "KHS" (mis. sheet "KR"
        // dan "Ganter AMR"), supaya seluruh data KHS berada dalam satu
        // kelompok di Sidebar.
        $khsTambahan = ['KR', 'GANTER AMR'];

        foreach ($sheets as $sheet) {
            $kategori  = strtoupper(trim($sheet->kategori ?? ''));
            $namaSheet = strtoupper(trim($sheet->nama_sheet ?? ''));

            // ---- Hapus BKJ & Sheet1 dari Sidebar ----
            if ($kategori === 'BKJ' || $namaSheet === 'BKJ') {
                continue;
            }
            if ($kategori === 'SHEET1' || str_starts_with($namaSheet, 'SHEET1')) {
                continue;
            }

            // ---- Kelompok Reservasi (flat, tanpa sub-menu) ----
            if (str_starts_with($kategori, 'RES')) {
                $menu['Reservasi']['type'] = 'flat';
                $menu['Reservasi']['items'][] = $sheet;
                continue;
            }

            // ---- Kelompok Rekap (flat, tanpa sub-menu) ----
            if (str_starts_with($kategori, 'REKAP')) {
                $menu['Rekap']['type'] = 'flat';
                $menu['Rekap']['items'][] = $sheet;
                continue;
            }

            // ---- Kelompok KHS (nested, per sub-kategori) ----
            // TAMBAHAN: Sheet "KR" & "Ganter AMR" ikut dimasukkan ke sini
            // (bukan lagi jadi menu terpisah), dan seluruh tahun pada
            // "KHS Pembesian" (2024/2025/2026, dst) otomatis tergabung
            // jadi satu sub-menu karena tahunnya sudah dipisah dari
            // kategori sejak proses upload Excel.
            if (str_starts_with($kategori, 'KHS') || in_array($kategori, $khsTambahan, true)) {
                $subKategori = $this->formatSubKategoriLabel($sheet->kategori);
                $menu['KHS']['type'] = 'nested';
                $menu['KHS']['children'][$subKategori][] = $sheet;
                continue;
            }

            // ---- Kategori lain di luar pola KHS/Rekap/Reservasi ----
            // Tetap ditampilkan apa adanya sebagai grup tersendiri
            // (flat) supaya tidak ada sheet yang hilang dari Sidebar.
            $label = $sheet->kategori ?: 'Lainnya';
            $menu[$label]['type'] = 'flat';
            $menu[$label]['items'][] = $sheet;
        }

        // TAMBAHAN: Urutan Sidebar -> KHS, Reservasi, kategori lain (A-Z),
        // dan Rekap selalu paling bawah.
        $prioritas = ['KHS' => 0, 'Reservasi' => 1];
        uksort($menu, function ($a, $b) use ($prioritas) {
            $posA = $this->sidebarSortWeight($a, $prioritas);
            $posB = $this->sidebarSortWeight($b, $prioritas);
            if ($posA === $posB) {
                return strcmp($a, $b);
            }
            return $posA <=> $posB;
        });

        return $menu;
    }

    // ==========================
    // TAMBAHAN: Bobot urutan grup Sidebar.
    // Rekap selalu ditaruh paling bawah, KHS & Reservasi paling atas,
    // kategori lain (mis. Monitoring Tiang, Monitoring Permohonan
    // Pelanggan) mengisi bagian tengah sesuai urutan alfabet.
    // ==========================
    private function sidebarSortWeight($label, array $prioritas)
    {
        if ($label === 'Rekap') {
            return 999;
        }

        return $prioritas[$label] ?? 500;
    }

    // ==========================
    // TAMBAHAN: Rapikan tampilan label sub-menu KHS.
    // Kategori disimpan uppercase di database (mis. "KHS PEMBESIAN",
    // "GANTER AMR"), fungsi ini menampilkannya jadi "KHS Pembesian",
    // "Ganter AMR", dst, sambil menjaga akronim tetap kapital.
    // ==========================
    private function formatSubKategoriLabel($kategoriAsli)
    {
        $akronim = ['KHS', 'KR', 'AMR', 'PLN'];

        $kata = preg_split('/\s+/', trim((string) $kategoriAsli));

        $kata = array_map(function ($w) use ($akronim) {
            $upper = strtoupper($w);
            return in_array($upper, $akronim, true) ? $upper : ucfirst(strtolower($w));
        }, $kata);

        return implode(' ', $kata);
    }

    // ==========================
    // TAMBAHAN: Data Grafik & Ringkasan Dashboard
    // Sumber: sheet "KHS Jasa 2026" (hasil upload Excel terbaru dari Admin)
    // Dihitung per Vendor: Total KR, Total Nilai Rupiah, serta rincian
    // Status (Progress / Selesai) beserta nilainya masing-masing.
    // Semua angka dihitung otomatis dari database, tidak ada data statis.
    // ==========================
    private function getKhsJasaChartData()
    {
        $tahunTarget    = '2026';
        $kategoriTarget = 'KHS JASA';

        // Ambil sheet "KHS Jasa 2026" -> dicocokkan lewat kategori (KHS Jasa,
        // tahun dipisah otomatis saat upload) ATAU nama sheet persis "KHS Jasa 2026",
        // supaya tetap terbaca walau format penamaan sheet sedikit berbeda.
        $sheets = Sheet::where(function ($query) use ($kategoriTarget, $tahunTarget) {
            $query->where(function ($q) use ($kategoriTarget, $tahunTarget) {
                $q->whereRaw('UPPER(kategori) = ?', [$kategoriTarget])
                    ->where('tahun', $tahunTarget);
            })->orWhereRaw('LOWER(nama_sheet) = ?', [strtolower('KHS Jasa ' . $tahunTarget)]);
        })->get();

        $kosong = [
            'has_data'   => false,
            'vendors'    => [],
            'per_vendor' => [],
            'summary'    => [
                'total_kr'    => 0,
                'total_nilai' => 0,
                'progress'    => ['kr' => 0, 'nilai' => 0],
                'selesai'     => ['kr' => 0, 'nilai' => 0],
            ],
        ];

        if ($sheets->isEmpty()) {
            return $kosong;
        }

        $vendorData = [];
        // 'Nama Vendor' => [
        //     'total_kr' => int, 'total_nilai' => float,
        //     'progress' => ['kr' => int, 'nilai' => float],
        //     'selesai'  => ['kr' => int, 'nilai' => float],
        // ]

        foreach ($sheets as $sheet) {

            $rows = SheetData::where('sheet_id', $sheet->id)
                ->orderBy('row_number')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            // Cari baris header: baris yang punya kolom Vendor, Status & Nilai
            $vendorCol = null;
            $statusCol = null;
            $nilaiCol  = null;
            $headerRowNumber = null;

            foreach ($rows->take(10) as $row) { // header biasanya ada di 10 baris pertama
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                foreach ($cells as $colIndex => $cellValue) {
                    $val = strtolower(trim((string) $cellValue));
                    if ($val === '') {
                        continue;
                    }
                    if (str_contains($val, 'vendor') || str_contains($val, 'penyedia')) {
                        $vendorCol = $colIndex;
                    }
                    if (str_contains($val, 'status')) {
                        $statusCol = $colIndex;
                    }
                    if (
                        $nilaiCol === null &&
                        (str_contains($val, 'nilai') || str_contains($val, 'harga') || str_contains($val, 'rupiah'))
                    ) {
                        $nilaiCol = $colIndex;
                    }
                }

                if ($vendorCol !== null && $nilaiCol !== null) {
                    $headerRowNumber = $row->row_number;
                    break;
                }

                // reset jika belum ketemu kolom wajib (Vendor & Nilai) di baris ini
                $vendorCol = null;
                $statusCol = null;
                $nilaiCol  = null;
            }

            // Kalau sheet ini tidak punya kolom Vendor & Nilai, lewati
            if ($headerRowNumber === null) {
                continue;
            }

            // Loop baris data (setelah baris header)
            foreach ($rows as $row) {
                if ($row->row_number <= $headerRowNumber) {
                    continue;
                }

                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                $vendorName = trim((string) ($cells[$vendorCol] ?? ''));
                if ($vendorName === '') {
                    continue;
                }

                $nilai     = $this->parseNilaiRupiah($cells[$nilaiCol] ?? 0);
                $statusRaw = $statusCol !== null
                    ? strtolower(trim((string) ($cells[$statusCol] ?? '')))
                    : '';

                // TAMBAHAN: label "Belum" tidak dipakai lagi di Dashboard.
                // Semua status selain "Selesai" (termasuk "Belum"/kosong)
                // dihitung sebagai "Progress" supaya konsisten dengan grafik.
                $statusField = str_contains($statusRaw, 'selesai') ? 'selesai' : 'progress';

                if (!isset($vendorData[$vendorName])) {
                    $vendorData[$vendorName] = [
                        'total_kr'    => 0,
                        'total_nilai' => 0,
                        'progress'    => ['kr' => 0, 'nilai' => 0],
                        'selesai'     => ['kr' => 0, 'nilai' => 0],
                    ];
                }

                $vendorData[$vendorName]['total_kr']++;
                $vendorData[$vendorName]['total_nilai'] += $nilai;
                $vendorData[$vendorName][$statusField]['kr']++;
                $vendorData[$vendorName][$statusField]['nilai'] += $nilai;
            }
        }

        if (empty($vendorData)) {
            return $kosong;
        }

        ksort($vendorData);

        $summary = [
            'total_kr'    => 0,
            'total_nilai' => 0,
            'progress'    => ['kr' => 0, 'nilai' => 0],
            'selesai'     => ['kr' => 0, 'nilai' => 0],
        ];

        foreach ($vendorData as $v) {
            $summary['total_kr']          += $v['total_kr'];
            $summary['total_nilai']       += $v['total_nilai'];
            $summary['progress']['kr']    += $v['progress']['kr'];
            $summary['progress']['nilai'] += $v['progress']['nilai'];
            $summary['selesai']['kr']     += $v['selesai']['kr'];
            $summary['selesai']['nilai']  += $v['selesai']['nilai'];
        }

        return [
            'has_data'   => true,
            'vendors'    => array_keys($vendorData),
            'per_vendor' => $vendorData,
            'summary'    => $summary,
        ];
    }

    // ==========================
    // TAMBAHAN: Ubah nilai rupiah dari Excel (angka murni ATAU teks
    // seperti "Rp 2.500.000.000" / "2.500.000.000,00") menjadi angka
    // float supaya bisa dijumlahkan dengan benar.
    // ==========================
    private function parseNilaiRupiah($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $bersih = preg_replace('/[^0-9.,]/', '', (string) $value);

        if ($bersih === '') {
            return 0.0;
        }

        if (str_contains($bersih, ',')) {
            // Format Indonesia: titik = pemisah ribuan, koma = desimal
            $bersih = str_replace('.', '', $bersih);
            $bersih = str_replace(',', '.', $bersih);
        } else {
            // Tanpa koma: seluruh titik dianggap pemisah ribuan
            $bersih = str_replace('.', '', $bersih);
        }

        return is_numeric($bersih) ? (float) $bersih : 0.0;
    }

    // ==========================
    // Dashboard Admin
    // ==========================
    public function adminIndex()
    {
        // Cek login admin
        if (!session()->has('admin')) {
            return redirect()->route('login.admin');
        }

        // TAMBAHAN: Ambil semua data KHS dari database (terbaru di atas)
        $data_khs = Khs::latest()->get(); 

        // TAMBAHAN: Kirim data $data_khs ke view menggunakan compact
        return view('dashboard-admin.dashboard-admin', compact('data_khs'));
    }

    // ==========================
    // Tampilkan Sheet
    // ==========================
    public function show($id)
    {
        $sheet = Sheet::findOrFail($id);

        $rows = SheetData::where('sheet_id', $id)
            ->orderBy('row_number')
            ->get();

        // Sheet RES
        if (strtoupper(substr($sheet->nama_sheet, 0, 3)) == 'RES') {
            return view('sheet_res', compact('sheet', 'rows'));
        }

        // Sheet lainnya
        return view('sheet', compact('sheet', 'rows'));
    }
}