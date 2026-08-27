<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- TAMBAHAN: dipakai untuk debug sementara link vendor RES
use Illuminate\Http\Request;
use App\Models\Sheet;
use App\Models\SheetData;
use App\Models\Upload; // <-- TAMBAHAN: dipakai untuk sumber waktu "Update Terakhir"
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

        // TAMBAHAN: Data Grafik Progress (diambil dari sheet "Detail KR")
        $progressChart = $this->getProgressChartData();

        // TAMBAHAN: Data Grafik "Sisa Kuota Vendor" (diambil dari sheet
        // "KUOTA VENDOR" - terpisah total dari $progressChart di atas).
        $kuotaVendorChart = $this->getKuotaVendorChartData();

        // TAMBAHAN: Navigasi Dashboard -> Vendor & Nama Pelanggan
        // Vendor: pakai daftar vendor yang sama dengan Grafik Progress
        // (sudah diambil dari sheet "Detail KR", jadi konsisten & dari DB).
        $resMfsSheetId      = $this->findSheetIdByNama('RES MFS');
        $detailKr2026SheetId = $this->findSheetIdByNama('Detail KR', '2026');
        $pelangganList      = $this->getNamaPelangganList();

        return view('dashboard', compact(
            'sidebarMenu',
            'progressChart',
            'kuotaVendorChart',
            'resMfsSheetId',
            'detailKr2026SheetId',
            'pelangganList'
        ));
    }

    // ==========================
    // TAMBAHAN: Struktur Sidebar 3 modul utama sesuai jenis monitoring.
    // - Monitoring KHS       -> pakai struktur lama (KHS/Reservasi/Rekap dsb)
    //   yang sudah teruji, lewat buildKhsMenu().
    // - Monitoring Tiang     -> daftar sheet apa adanya, otomatis mengikuti
    //   sheet yang ada di database (upload baru langsung muncul).
    // - Monitoring Pelanggan -> sama seperti Monitoring Tiang.
    // ==========================
    private function buildSidebarMenu()
    {
        return [
            'Monitoring KHS' => [
                'type'   => 'monitoring-khs',
                'groups' => $this->buildKhsMenu(),
            ],
            'Monitoring Tiang' => [
                'type'  => 'flat',
                'items' => $this->filterAllowedSheets('tiang', [
                    'RPB per SPB',
                    'TA per SPB',
                    'Maxima per SPB',
                    'WIKA per SPB',
                    'Total Vendor',
                    'Rekap KR',
                ]),
            ],
            'Monitoring Pelanggan' => [
                'type'  => 'flat',
                'items' => $this->filterAllowedSheets('pelanggan', [
                    'Pelanggan 2026',
                ]),
            ],
        ];
    }

    // ==========================
    // TAMBAHAN: Batasi sheet yang tampil di Sidebar untuk Monitoring
    // Tiang & Monitoring Pelanggan, hanya sesuai daftar nama sheet yang
    // diizinkan (case-insensitive, tanpa spasi berlebih di awal/akhir).
    // Sheet lain yang ikut ter-upload dalam file Excel yang sama TETAP
    // tersimpan di database apa adanya (tidak dihapus / tidak diubah) -
    // ini hanya menyaring apa yang ditampilkan di Sidebar. Urutan hasil
    // mengikuti urutan $allowedNames, bukan urutan kolom "urutan" di DB,
    // supaya sesuai urutan yang diminta.
    // ==========================
    private function filterAllowedSheets(string $jenisMonitoring, array $allowedNames)
    {
        $sheets = Sheet::where('jenis_monitoring', $jenisMonitoring)
            ->orderBy('urutan')
            ->get();

        $allowedLower = array_map(fn ($n) => mb_strtolower(trim($n)), $allowedNames);

        return $sheets
            ->filter(function ($sheet) use ($allowedLower) {
                return in_array(mb_strtolower(trim($sheet->nama_sheet ?? '')), $allowedLower, true);
            })
            ->sortBy(function ($sheet) use ($allowedLower) {
                $pos = array_search(mb_strtolower(trim($sheet->nama_sheet ?? '')), $allowedLower, true);
                return $pos === false ? 999 : $pos;
            })
            ->values()
            ->all();
    }

    // ==========================
    // TAMBAHAN: Bangun struktur menu untuk Monitoring KHS saja
    // (persis logika Sidebar lama yang sudah teruji - KHS/Reservasi/Rekap
    // dsb - hanya sekarang di-scope ke jenis_monitoring = 'khs').
    // Hanya mengubah tampilan/pengelompokan, TIDAK mengubah data di database.
    // Pengelompokan otomatis berdasarkan pola nama/kategori sheet:
    // - Kategori diawali "RES"   -> masuk grup "Reservasi" (flat)
    // - Kategori diawali "REKAP" -> masuk grup "Rekap" (flat, selalu di paling bawah)
    // - Kategori diawali "KHS", atau nama sheet AMR/GANTER/KR PBPD -> masuk
    //   grup "KHS", dikelompokkan lagi per sub-kategori (mis. "KHS Jasa").
    // - Kategori/nama sheet "BKJ" atau "Sheet1" -> disembunyikan
    // - Kategori lain di luar itu -> tetap ditampilkan apa adanya
    //   sebagai grup tersendiri, supaya tidak ada data yang hilang
    //   dari Sidebar meskipun belum termasuk pola di atas.
    // ==========================
    private function buildKhsMenu()
    {
        $sheets = Sheet::where('jenis_monitoring', 'khs')
            ->orderBy('kategori')
            ->orderBy('tahun')
            ->orderBy('urutan')
            ->get();

        $menu = [];

        // TAMBAHAN: Sheet yang secara khusus harus ikut masuk ke kelompok
        // KHS meskipun kategorinya sendiri tidak diawali "KHS"
        // (mis. AMR 2026, Ganter 2026, KR PBPD).
        $khsTambahan = ['AMR', 'GANTER', 'KR PBPD'];

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

            // TAMBAHAN: Sembunyikan sheet "REKAP" dari Sidebar/menu website
            // (permintaan user). Dicocokkan PERSIS (bukan awalan/substring)
            // pada nama sheet supaya sheet lain yang namanya diawali kata
            // "REKAP" (mis. "REKAP CONNECTOR", "REKAP PEMBESIAN 2026")
            // TIDAK ikut tersembunyi - hanya sheet yang namanya benar-benar
            // "REKAP" saja. Sheet ini TIDAK dihapus dari database/spreadsheet,
            // hanya tidak ditampilkan di daftar menu.
            if ($namaSheet === 'REKAP') {
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

            // ---- Cek apakah sheet ini termasuk "tambahan" KHS ----
            $isKhsTambahan = false;
            foreach ($khsTambahan as $prefix) {
                if ($namaSheet === $prefix || str_starts_with($namaSheet, $prefix . ' ')) {
                    $isKhsTambahan = true;
                    break;
                }
            }

            // ---- Kelompok KHS (nested, per sub-kategori) ----
            if (str_starts_with($kategori, 'KHS') || $isKhsTambahan) {
                // simpan sub-kategori casing asli utk tampilan; kalau sheet
                // tambahan (AMR/GANTER/KR PBPD) belum punya kategori KHS,
                // pakai kategori aslinya sendiri sebagai sub-kelompok.
                $subKategori = $sheet->kategori ?: $sheet->nama_sheet;
                $menu['KHS']['type'] = 'nested';

                // TAMBAHAN: Gabungkan seluruh sub-kategori "KHS Pembesian ..."
                // (per tahun, mis. "KHS Pembesian 2024" & "KHS Pembesian
                // 2025-2026") ke dalam satu menu induk "KHS Pembesian" dengan
                // submenu per tahun, supaya tidak tampil sebagai menu
                // terpisah-pisah di Sidebar. Tahun baru yang di-upload nanti
                // otomatis ikut masuk ke sini juga karena hanya dicek dari
                // awalan kategorinya.
                if (str_starts_with($subKategori, 'KHS PEMBESIAN')) {
                    // TAMBAHAN: label ditulis kapital semua ("KHS PEMBESIAN")
                    // supaya konsisten dengan penulisan menu KHS lain yang
                    // memang tampil uppercase (mis. "KHS JASA", "KHS COVER
                    // 2024-2026"). Hanya mengubah label tampilan di Sidebar,
                    // bukan nama sheet di database.
                    $menu['KHS']['children']['KHS PEMBESIAN'][$subKategori][] = $sheet;
                } else {
                    $menu['KHS']['children'][$subKategori][] = $sheet;
                }
                continue;
            }

            // ---- Kategori lain di luar pola KHS/Rekap/Reservasi ----
            // Tetap ditampilkan apa adanya sebagai grup tersendiri
            // (flat) supaya tidak ada sheet yang hilang dari Sidebar.
            $label = $sheet->kategori ?: 'Lainnya';
            $menu[$label]['type'] = 'flat';
            $menu[$label]['items'][] = $sheet;
        }

        // Urutkan Sidebar sesuai urutan yang diinginkan:
        // KHS, Reservasi, lalu kategori lain apa adanya, dan Rekap SELALU
        // di posisi paling bawah.
        $prioritas = [
            'KHS'                             => 0,
            'Reservasi'                       => 1,
            'Monitoring PBJ'                  => 2,
            'Monitoring Permohonan Pelanggan' => 3,
            'Monitoring Tiang'                => 4,
            'Detail KR'                       => 5,
        ];
        uksort($menu, function ($a, $b) use ($prioritas) {
            // Rekap selalu paling bawah, apa pun yang lain.
            if ($a === 'Rekap' || $b === 'Rekap') {
                if ($a === $b) {
                    return 0;
                }
                return $a === 'Rekap' ? 1 : -1;
            }

            $posA = $prioritas[$a] ?? 99;
            $posB = $prioritas[$b] ?? 99;
            if ($posA === $posB) {
                return strcmp($a, $b);
            }
            return $posA <=> $posB;
        });

        return $menu;
    }

    // ==========================
    // PERBAIKAN: Data Grafik Progress per Vendor
    // Sumber: HANYA sheet "DETAIL KR 2026" (BUKAN "KHS JASA 2026" ataupun
    // sheet lain). Data DIAGREGASI per Vendor Pelaksana:
    //   - Progress (%) = rata-rata kolom "Status (%)" seluruh KR/Paket
    //     unik milik vendor tsb (BUKAN lagi jumlah/hitungan KR).
    //   - Selesai (%)  = persentase KR/Paket yang BENAR-BENAR selesai
    //     (seluruh baris Lokasi Pekerjaan pada KR tsb sudah 100%)
    //     terhadap total KR milik vendor (BUKAN lagi jumlah/hitungan KR).
    //   - Nilai KR = dijumlahkan dari KR/Paket UNIK milik vendor tsb
    //     (dibaca langsung dari kolom "Nilai Kontrak"), SATU KR yang
    //     punya beberapa baris Nama Pekerjaan/Lokasi TETAP dihitung
    //     nilainya SEKALI SAJA - tidak dijumlahkan berulang per baris.
    //   - Daftar Lokasi Pekerjaan (beserta Status (%) masing-masing)
    //     milik vendor tsb (untuk ditampilkan di tooltip saat hover).
    // Kolom yang diambil (dibaca dari isi header, bukan tebak posisi):
    //   - Vendor Pelaksana
    //   - Lokasi Pekerjaan
    //   - Nilai Kontrak
    //   - Status (%)
    // ==========================
    private function getProgressChartData()
    {
        // ==========================
        // PERBAIKAN (root cause "data lama tidak hilang setelah dihapus &
        // upload ulang"): sebelumnya query di sini TIDAK di-scope ke
        // jenis_monitoring, beda dengan fungsi lain di controller ini
        // (filterAllowedSheets(), buildKhsMenu()) yang sudah benar
        // men-scope ke jenis_monitoring. Proses upload (importExcel() di
        // InputDataController) menghapus sheet lama HANYA untuk
        // jenis_monitoring yang sama dengan upload yang sedang jalan -
        // jadi kalau ada sheet "DETAIL KR" tersisa di jenis_monitoring lain
        // (mis. sisa dari upload yang salah pintu, atau data lama sebelum
        // kolom jenis_monitoring ada - migrasinya default ke 'khs'),
        // sheet tsb TIDAK PERNAH ikut terhapus saat upload ulang, dan versi
        // KODE LAMA di sini akan tetap mengambil & MENGGABUNGKAN datanya ke
        // grafik bersama sheet yang benar - sehingga data yang sudah
        // dihapus di Excel seolah masih "nyangkut" di grafik.
        //
        // FIX: sheet "DETAIL KR 2026" adalah bagian dari workbook
        // Monitoring KHS (lihat buildKhsMenu() - dikelompokkan bersama
        // AMR/GANTER/KR PBPD di bawah jenis_monitoring = 'khs'), jadi query
        // di sini di-scope ke jenis_monitoring = 'khs' juga, SUPAYA
        // konsisten dengan cara importExcel() menghapus data lama.
        //
        // FIX LAPIS KEDUA: walau sudah di-scope, tetap dijaga-jaga kalau
        // suatu saat ada lebih dari satu sheet "DETAIL KR 2026" yang lolos
        // (mis. karena upload gagal di tengah jalan sebelumnya) - HANYA
        // SATU sheet TERBARU (created_at/id terbesar) yang dipakai, sisanya
        // diabaikan (TIDAK digabung/dijumlahkan bersama).
        // ==========================
        $jenisMonitoringDetailKr = 'khs';

        $sheetsDitemukan = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%detail kr%'])
            ->where('jenis_monitoring', $jenisMonitoringDetailKr)
            ->get();

        \Illuminate\Support\Facades\Log::info('DETAIL KR FOUND: ' . $sheetsDitemukan->count() . ' sheet (jenis_monitoring=' . $jenisMonitoringDetailKr . ')');
        foreach ($sheetsDitemukan as $s) {
            \Illuminate\Support\Facades\Log::info(
                'ID: ' . $s->id . ' | NAME: ' . $s->nama_sheet . ' | YEAR: ' . $s->tahun
                . ' | CREATED_AT: ' . $s->created_at
            );
        }

        $sheets2026 = $sheetsDitemukan->filter(function ($s) {
            return (string) $s->tahun === '2026' || str_contains((string) $s->nama_sheet, '2026');
        });

        if ($sheets2026->isNotEmpty()) {
            $sheetsDitemukan = $sheets2026;
        }

        \Illuminate\Support\Facades\Log::info('DETAIL KR 2026 candidates: ' . $sheetsDitemukan->count());

        // PERBAIKAN: HANYA satu sheet (paling baru) yang dipakai - tidak
        // pernah lebih dari satu, supaya tidak ada penggabungan/penjumlahan
        // data dari beberapa record sheet DETAIL KR 2026 sekaligus.
        $sheetAktif = $sheetsDitemukan
            ->sortByDesc(fn ($s) => [$s->created_at, $s->id])
            ->first();

        $sheets = $sheetAktif ? collect([$sheetAktif]) : collect();

        if ($sheetAktif) {
            $jumlahSheetData = SheetData::where('sheet_id', $sheetAktif->id)->count();
            \Illuminate\Support\Facades\Log::info(
                'DETAIL KR 2026 ACTIVE SHEET USED -> ID: ' . $sheetAktif->id
                . ' | NAME: ' . $sheetAktif->nama_sheet
                . ' | YEAR: ' . $sheetAktif->tahun
                . ' | SheetData rows: ' . $jumlahSheetData
            );
        } else {
            \Illuminate\Support\Facades\Log::info('DETAIL KR 2026 ACTIVE SHEET USED -> tidak ditemukan sheet yang cocok.');
        }

        // Agregasi per Vendor Pelaksana. Key = nama vendor yang sudah
        // DINORMALISASI (huruf besar semua + spasi ganda/berlebih dirapikan)
        // lewat normalizeVendorName(), supaya baris-baris milik vendor yang
        // sama TIDAK terpecah jadi vendor berbeda di grafik hanya karena
        // perbedaan huruf besar/kecil atau spasi pada sheet "DETAIL KR 2026"
        // (mis. "PT Sinar Kumbang Sakti" vs "PT SINAR KUMBANG  SAKTI").
        // Label yang ditampilkan di grafik/tooltip tetap memakai nama versi
        // pertama kali muncul (dirapikan spasinya saja, casing asli disimpan).
        $vendorAgg   = [];
        $vendorLabel = []; // key ternormalisasi => label asli utk ditampilkan
        $vendorOrder = []; // urutan kemunculan vendor (key ternormalisasi) pertama kali di data

        foreach ($sheets as $sheet) {

            $rows = SheetData::where('sheet_id', $sheet->id)
                ->orderBy('row_number')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            // Cari baris header: baris yang punya kolom Vendor Pelaksana,
            // Lokasi Pekerjaan, Nilai Kontrak, DAN Status (%) sekaligus.
            // Tidak menebak posisi kolom - murni dibaca dari isi header.
            // Kolom "Kontrak Rinci" (nomor paket) DAN kolom "PRK" ikut
            // dicari juga - keduanya dipakai untuk mengenali baris-baris
            // mana saja yang sebenarnya satu paket/kontrak yang sama
            // (lihat catatan di bawah). PERBAIKAN: sebelumnya hanya kolom
            // yang mengandung kata "kontrak" yang dikenali sebagai identitas
            // paket, sehingga kalau sheet memakai header "PRK" (tanpa kata
            // "kontrak") kolom itu tidak pernah terdeteksi dan dedup jatuh
            // ke fallback nilai kontrak mentah yang rapuh - ini bisa jadi
            // penyebab Nilai Kontrak masih terhitung berlipat.
            $vendorCol = null;
            $lokasiCol = null;
            $nilaiKontrakCol = null;
            $statusCol = null;
            $kontrakRinciCol = null;
            $prkCol = null;
            $headerRowNumber = null;

            foreach ($rows->take(15) as $row) { // header biasanya ada di baris-baris awal
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                $foundVendor = null;
                $foundLokasi = null;
                $foundNilaiKontrak = null;
                $foundStatus = null;
                $foundKontrakRinci = null;
                $foundPrk = null;

                foreach ($cells as $colIndex => $cellValue) {
                    $val = strtolower(trim((string) $cellValue));
                    if ($val === '') {
                        continue;
                    }

                    if ($foundVendor === null && str_contains($val, 'vendor')) {
                        $foundVendor = $colIndex;
                        continue;
                    }

                    if ($foundLokasi === null && str_contains($val, 'lokasi')) {
                        $foundLokasi = $colIndex;
                        continue;
                    }

                    // Kolom "Nilai Kontrak" dicek sebelum kolom status supaya
                    // tidak tertukar kalau ada kata yang mirip.
                    if ($foundNilaiKontrak === null
                        && str_contains($val, 'nilai')
                        && str_contains($val, 'kontrak')) {
                        $foundNilaiKontrak = $colIndex;
                        continue;
                    }

                    // Kolom "Kontrak Rinci" (nomor PAKET) - dipakai untuk tahu
                    // baris mana saja yang sebenarnya satu paket/kontrak yang
                    // sama, supaya Nilai Kontrak tidak dijumlahkan berulang.
                    // Dicek sesudah "Nilai Kontrak" (di atas) supaya tidak
                    // salah tertangkap sebagai kolom ini.
                    if ($foundKontrakRinci === null
                        && str_contains($val, 'kontrak')
                        && !str_contains($val, 'nilai')) {
                        $foundKontrakRinci = $colIndex;
                        continue;
                    }

                    // PERBAIKAN: kolom "PRK" (mis. "PRK", "No PRK", "Nomor
                    // PRK") juga dikenali sebagai identitas KR/Paket, selain
                    // "Kontrak Rinci". Dicek pakai word-boundary (\bprk\b)
                    // supaya tidak salah cocok dengan kata lain yang
                    // kebetulan mengandung huruf "prk" di tengahnya.
                    if ($foundPrk === null && preg_match('/\bprk\b/', $val)) {
                        $foundPrk = $colIndex;
                        continue;
                    }

                    if ($foundStatus === null && str_contains($val, 'status')) {
                        $foundStatus = $colIndex;
                    }
                }

                if ($foundVendor !== null && $foundLokasi !== null
                    && $foundNilaiKontrak !== null && $foundStatus !== null) {
                    $vendorCol       = $foundVendor;
                    $lokasiCol       = $foundLokasi;
                    $nilaiKontrakCol = $foundNilaiKontrak;
                    $statusCol       = $foundStatus;
                    $kontrakRinciCol = $foundKontrakRinci; // boleh null, opsional
                    $prkCol          = $foundPrk;          // boleh null, opsional
                    $headerRowNumber = $row->row_number;
                    break;
                }
            }

            // Kalau sheet ini tidak punya keempat kolom yang dibutuhkan lengkap, lewati.
            if ($headerRowNumber === null) {
                continue;
            }

            // Loop baris data (setelah baris header). Setiap baris = satu
            // Lokasi Pekerjaan, TIDAK digabung ke vendor lain.
            foreach ($rows as $row) {
                if ($row->row_number <= $headerRowNumber) {
                    continue;
                }

                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                $vendorAsli = trim((string) ($cells[$vendorCol] ?? ''));
                $lokasiRaw  = trim((string) ($cells[$lokasiCol] ?? ''));

                // Baris tanpa Vendor atau tanpa Lokasi Pekerjaan dilewati
                // (bukan data KR yang valid untuk ditampilkan di grafik).
                if ($vendorAsli === '' || $lokasiRaw === '') {
                    continue;
                }

                // PERBAIKAN: normalisasi nama vendor (huruf besar semua +
                // rapikan spasi) dipakai sebagai KUNCI pengelompokan, supaya
                // vendor yang sama tidak terpecah gara-gara beda huruf
                // besar/kecil atau spasi ganda pada sheet "DETAIL KR 2026".
                $vendorKey = $this->normalizeVendorName($vendorAsli);
                if ($vendorKey === '') {
                    continue;
                }

                $nilaiKontrakRaw = $cells[$nilaiKontrakCol] ?? 0;
                $statusRaw       = $cells[$statusCol] ?? 0;

                $nilai  = $this->parseNilaiRupiah($nilaiKontrakRaw);
                $status = $this->parseStatusPersen($statusRaw);

                if (!isset($vendorAgg[$vendorKey])) {
                    $vendorAgg[$vendorKey] = [
                        'paket' => [], // dikumpulkan per KR/Paket dulu (lihat catatan di bawah)
                    ];
                    // Label tampilan pakai kemunculan pertama, spasi
                    // dirapikan tapi huruf besar/kecil aslinya tetap dijaga
                    // supaya tidak mengubah penulisan asli di Excel.
                    $vendorLabel[$vendorKey] = preg_replace('/\s+/', ' ', $vendorAsli);
                    $vendorOrder[] = $vendorKey;
                }

                // PERBAIKAN: satu baris di sheet "DETAIL KR 2026" = satu
                // Nama Pekerjaan/Lokasi, TAPI beberapa baris bisa jadi
                // milik SATU KR/Paket yang sama (dikenali dari kolom
                // "Kontrak Rinci" - mis. "PAKET 132" muncul di 6 baris
                // lokasi berbeda). Supaya Total KR, Progress, Selesai, dan
                // Nilai Kontrak mengikuti perhitungan spreadsheet (1 KR
                // tetap dihitung 1, bukan sebanyak baris/lokasinya), semua
                // baris dikumpulkan dulu per KUNCI PAKET di sini - baru
                // dihitung jadi 1 titik data per KR pada tahap agregasi di
                // bawah (setelah seluruh sheet selesai dibaca).
                //
                // PERBAIKAN: kunci identitas KR/Paket sekarang digabung dari
                // "Kontrak Rinci" DAN "PRK" (kalau kolomnya ada dan terisi),
                // bukan cuma salah satu. Ini supaya lebih tahan terhadap
                // sheet yang memakai nama kolom berbeda-beda untuk identitas
                // KR yang sama - vendor + gabungan ini yang menentukan
                // baris-baris mana yang sebenarnya satu KR/Paket yang sama.
                // Kalau KEDUA kolom itu tidak ada/kosong di sheet, baru
                // dipakai fallback teks asli "Nilai Kontrak" sebagai kunci
                // (sama seperti sebelumnya). Kalau semuanya kosong, baris
                // tsb dianggap 1 KR tersendiri (pakai row_number sebagai
                // kunci unik) supaya tetap ikut terhitung, bukan hilang.
                $kontrakRinciRaw = $kontrakRinciCol !== null
                    ? trim((string) ($cells[$kontrakRinciCol] ?? ''))
                    : '';
                $prkRaw = $prkCol !== null
                    ? trim((string) ($cells[$prkCol] ?? ''))
                    : '';

                $identParts = array_filter([$kontrakRinciRaw, $prkRaw], fn ($v) => $v !== '');
                $kunciPaket = !empty($identParts)
                    ? implode('|', $identParts)
                    : trim((string) $nilaiKontrakRaw);
                if ($kunciPaket === '') {
                    $kunciPaket = '__row_' . $row->row_number;
                }

                // TAMBAHAN: label paket yang dipakai untuk RINCIAN NILAI
                // KONTRAK per paket di tooltip (mis. "PAKET 126"). Diambil
                // dari "Kontrak Rinci" kalau ada, kalau tidak dari "PRK",
                // kalau dua-duanya tidak ada dipakai label generik urut
                // ("Paket 1", "Paket 2", dst - dihitung nanti saat baris
                // pertama paket ini dibuat, bukan disamakan dengan
                // nilai/lokasi supaya tetap jelas dibedakan per KR).
                $labelPaket = $kontrakRinciRaw !== ''
                    ? $kontrakRinciRaw
                    : ($prkRaw !== '' ? $prkRaw : '');

                if (!isset($vendorAgg[$vendorKey]['paket'][$kunciPaket])) {
                    $vendorAgg[$vendorKey]['paket'][$kunciPaket] = [
                        'label'    => $labelPaket, // boleh kosong - diisi fallback nanti saat ditampilkan
                        'nilai'    => $nilai, // Nilai Kontrak sama utk semua baris 1 paket - ambil sekali saja
                        'statuses' => [],
                        'lokasi'   => [],
                    ];
                }

                $vendorAgg[$vendorKey]['paket'][$kunciPaket]['statuses'][] = $status;
                $vendorAgg[$vendorKey]['paket'][$kunciPaket]['lokasi'][]   = $lokasiRaw;
            }
        }

        $vendors         = [];
        $progressPersen  = []; // PERBAIKAN: sekarang berisi persentase (0-100) dari kolom "Status (%)", BUKAN jumlah KR lagi
        $selesaiPersen   = []; // PERBAIKAN: persentase KR yang BENAR-BENAR selesai (100%) terhadap total KR vendor, BUKAN jumlah KR lagi
        $lokasiPerVendor = [];
        $detailPerVendor = []; // rincian lengkap per vendor, dipakai tooltip

        foreach ($vendorOrder as $vendorKey) {
            $namaTampilan = $vendorLabel[$vendorKey];

            // PERBAIKAN (root cause Rp356 juta): SEBELUMNYA di sini ada
            // `$totalNilai += $paket['nilai']` di dalam loop paket - baris
            // itu MENJUMLAHKAN Nilai Kontrak dari SEMUA paket/KR milik satu
            // vendor jadi SATU angka ("Nilai KR"). Padahal masing-masing
            // paket (mis. PAKET 126, PAKET 130, dst) adalah KONTRAK YANG
            // BERBEDA dengan nilai kontraknya SENDIRI-SENDIRI - menjumlahkan
            // semuanya jadi satu angka tidak punya arti apa-apa di
            // spreadsheet aslinya dan itulah yang membuat PT MEGA FAMILY
            // SUKSES (nilai 1 paketnya Rp17.525.906) muncul sebagai
            // Rp356.319.745 di dashboard.
            //
            // FIX: SUM dihapus total. Nilai Kontrak sekarang HANYA disimpan
            // per paket (lihat $paketDetail di bawah - satu baris per
            // paket/KR, nilainya diambil SEKALI dari data paket tsb, TIDAK
            // pernah dijumlahkan lintas paket). Tooltip vendor menampilkan
            // daftar "PAKET -> Nilai Kontrak" ini, bukan satu angka agregat.
            //
            // PERBAIKAN: Progress & Selesai berbasis kolom "Status (%)"
            // (persentase), BUKAN jumlah/hitungan KR:
            // - Progress (%) = rata-rata persentase status seluruh KR
            //   milik vendor (rata-rata dari status rata-rata tiap KR).
            // - Selesai (%)  = persentase KR yang BENAR-BENAR selesai
            //   (seluruh baris pekerjaan dalam KR tsb sudah 100%)
            //   terhadap total KR milik vendor.
            $totalKr           = 0;
            $selesaiKr         = 0;
            $jumlahStatusPaket = 0.0; // akumulasi rata-rata status per KR, dipakai utk Progress (%)
            $lokasiDetailSemua = []; // ['lokasi' => ..., 'status' => ...] per baris, utk tooltip
            $paketDetail       = []; // TAMBAHAN: rincian Nilai Kontrak PER PAKET/KR (bukan agregat)
            $paketKe           = 0;  // dipakai utk label fallback "Paket 1", "Paket 2", dst

            foreach ($vendorAgg[$vendorKey]['paket'] as $paket) {
                $totalKr++;
                $paketKe++;

                $jumlahStatus = 0.0;
                $isSelesai    = count($paket['statuses']) > 0;

                foreach ($paket['statuses'] as $s) {
                    $jumlahStatus += $s;
                    // Satu KR/Paket dianggap SELESAI hanya kalau SELURUH
                    // baris Nama Pekerjaan/Lokasi di dalam KR tsb sudah
                    // 100% (tidak ada satupun yang masih di bawah 100%) -
                    // ini mengikuti makna "KR selesai" pada spreadsheet.
                    // Toleransi kecil (>= 99.995) untuk pembulatan.
                    if ($s < 99.995) {
                        $isSelesai = false;
                    }
                }

                $statusRataPaket = count($paket['statuses']) > 0
                    ? $jumlahStatus / count($paket['statuses'])
                    : 0.0;
                $jumlahStatusPaket += $statusRataPaket;

                if ($isSelesai) {
                    $selesaiKr++;
                }

                foreach ($paket['lokasi'] as $idxLok => $lok) {
                    $lokasiDetailSemua[] = [
                        'lokasi' => $lok,
                        'status' => $paket['statuses'][$idxLok] ?? $statusRataPaket,
                    ];
                }

                // TAMBAHAN: satu entri per PAKET/KR unik, Nilai Kontrak
                // diambil SEKALI (persis seperti $paket['nilai'] - sudah
                // dedup sejak tahap pengumpulan di atas, TIDAK dijumlahkan
                // dengan paket lain di sini).
                // TAMBAHAN: status per paket/KR (selesai / masih progress)
                // ikut disimpan di sini - dipakai FRONTEND untuk memfilter
                // tabel "Nilai KR / Nilai Kontrak per Paket" saat tombol
                // "KR Selesai" / "KR Progress" / "Total KR" diklik. Logika
                // "selesai"-nya SAMA PERSIS dengan $isSelesai di atas
                // (dipakai juga untuk menghitung $selesaiKr) - tidak ada
                // perhitungan status baru/berbeda yang dibuat di sini.
                $paketDetail[] = [
                    'paket'   => $paket['label'] !== '' ? $paket['label'] : ('Paket ' . $paketKe),
                    'nilai'   => round($paket['nilai'], 2),
                    'lokasi'  => array_values($paket['lokasi']),
                    'selesai' => $isSelesai,
                ];
            }

            $progressPersenV = $totalKr > 0 ? round($jumlahStatusPaket / $totalKr, 2) : 0.0;
            $selesaiPersenV  = $totalKr > 0 ? round(($selesaiKr / $totalKr) * 100, 2) : 0.0;

            $lokasiNamaSaja = array_map(fn ($l) => $l['lokasi'], $lokasiDetailSemua);

            $vendors[]          = $namaTampilan;
            $progressPersen[]   = $progressPersenV;
            $selesaiPersen[]    = $selesaiPersenV;
            $lokasiPerVendor[]  = array_values($lokasiNamaSaja);

            $detailPerVendor[] = [
                'vendor'          => $namaTampilan,
                'total_kr'        => $totalKr,
                'paket_detail'    => $paketDetail, // TAMBAHAN: [{paket, nilai, lokasi}] - NILAI PER PAKET, bukan agregat
                'progress_persen' => $progressPersenV,
                'selesai_persen'  => $selesaiPersenV,
                'selesai_kr'      => $selesaiKr,
                'lokasi'          => array_values($lokasiNamaSaja),
                'lokasi_detail'   => array_values($lokasiDetailSemua), // [{lokasi, status}] utk tooltip
            ];
        }

        \Illuminate\Support\Facades\Log::info('DETAIL KR 2026 vendor ditemukan: ' . count($vendors));

        return [
            'vendors'       => $vendors,
            'progress'      => $progressPersen, // persentase (0-100), BUKAN jumlah KR
            'selesai'       => $selesaiPersen,  // persentase (0-100), BUKAN jumlah KR
            // PERBAIKAN: 'nilai_kr' (agregat SUM semua paket per vendor)
            // DIHAPUS - itulah sumber bug Rp356 juta. Rincian Nilai Kontrak
            // sekarang HANYA ada per paket, lihat detail_vendor[].paket_detail.
            'lokasi_vendor' => $lokasiPerVendor,
            'detail_vendor' => $detailPerVendor,
        ];
    }

    // ==========================
    // TAMBAHAN: Data Grafik "Sisa Kuota Vendor"
    // Sumber: HANYA sheet "KUOTA VENDOR" (terpisah sepenuhnya dari
    // getProgressChartData()/sheet "DETAIL KR 2026" di atas - tidak
    // menyentuh logika/data grafik tsb sama sekali).
    //
    // Setiap BARIS data pada sheet "KUOTA VENDOR" menjadi SATU titik data
    // (SATU batang) pada grafik - TIDAK ada penjumlahan/agregasi apa pun
    // walau ada vendor yang muncul di lebih dari satu baris (mis. beda
    // No Kontrak) - persis mengikuti perintah "gunakan record/No Kontrak
    // yang benar, jangan menjumlahkan". Nilai batang HANYA dari kolom
    // "Sisa Kuota (%)", kolom lain (Nilai Kontrak, AMD I, Realisasi KR
    // Terkontrak, Sisa Kuota Rp) hanya ikut disimpan untuk detail saat
    // batang diklik.
    //
    // PERBAIKAN link/mapping vendor (mis. "PT SKORPION PERMATA MANDIRI",
    // "PT PATHUR TEKNIK MANDIRI"): grafik ini TIDAK mencocokkan/mencari
    // vendor berdasarkan namanya sama sekali - setiap baris tampil apa
    // adanya berdasarkan URUTAN (index) yang sama antara array vendor,
    // nilai grafik, dan detail, sehingga tidak ada vendor yang bisa
    // "gagal terhubung" karena perbedaan penulisan nama. Nama vendor tetap
    // dirapikan (trim spasi berlebih) sebelum ditampilkan, dan baris hanya
    // dilewati kalau NAMA VENDOR-nya benar-benar kosong.
    // ==========================
    private function getKuotaVendorChartData()
    {
        $sheetsDitemukan = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%kuota vendor%'])->get();

        if ($sheetsDitemukan->isEmpty()) {
            return [
                'vendors' => [],
                'sisa_kuota_persen' => [],
                'detail_vendor' => [],
            ];
        }

        // Utamakan sheet "KUOTA VENDOR" tahun 2026 kalau ada lebih dari satu
        $sheets2026 = $sheetsDitemukan->filter(function ($s) {
            return (string) $s->tahun === '2026' || str_contains((string) $s->nama_sheet, '2026');
        });

        if ($sheets2026->isNotEmpty()) {
            $sheetsDitemukan = $sheets2026;
        }

        // Hanya SATU sheet (paling baru) yang dipakai - sama seperti
        // getProgressChartData() - supaya tidak ada data dobel/nyangkut
        // dari upload sebelumnya.
        $sheetAktif = $sheetsDitemukan->sortByDesc(fn ($s) => [$s->created_at, $s->id])->first();

        $rows = SheetData::where('sheet_id', $sheetAktif->id)
            ->orderBy('row_number')
            ->get();

        $kosong = [
            'vendors' => [],
            'sisa_kuota_persen' => [],
            'detail_vendor' => [],
        ];

        if ($rows->isEmpty()) {
            return $kosong;
        }

        // Cari baris header: baris yang punya kolom Vendor Pelaksana DAN
        // Sisa Kuota (%) sekaligus (kolom wajib). Kolom lain (No Kontrak,
        // Nilai Kontrak, AMD I, Realisasi KR Terkontrak, Sisa Kuota Rp)
        // dicari juga tapi bersifat opsional (boleh tidak ketemu, detail
        // tsb akan tampil kosong/"-").
        $vendorCol = null;
        $noKontrakCol = null;
        $nilaiKontrakCol = null;
        $amdCol = null;
        $realisasiCol = null;
        $sisaRpCol = null;
        $sisaPersenCol = null;
        $headerRowNumber = null;

        foreach ($rows->take(15) as $row) {
            $cells = json_decode($row->row_data, true);
            if (!is_array($cells)) {
                continue;
            }

            $foundVendor = null;
            $foundNoKontrak = null;
            $foundNilaiKontrak = null;
            $foundAmd = null;
            $foundRealisasi = null;
            $foundSisaRp = null;
            $foundSisaPersen = null;

            foreach ($cells as $colIndex => $cellValue) {
                $val = strtolower(trim((string) $cellValue));
                if ($val === '') {
                    continue;
                }

                if ($foundVendor === null && str_contains($val, 'vendor')) {
                    $foundVendor = $colIndex;
                    continue;
                }

                if ($foundNoKontrak === null && str_contains($val, 'no') && str_contains($val, 'kontrak')
                    && !str_contains($val, 'nilai')) {
                    $foundNoKontrak = $colIndex;
                    continue;
                }

                if ($foundNilaiKontrak === null && str_contains($val, 'nilai') && str_contains($val, 'kontrak')) {
                    $foundNilaiKontrak = $colIndex;
                    continue;
                }

                if ($foundAmd === null && str_contains($val, 'amd')) {
                    $foundAmd = $colIndex;
                    continue;
                }

                if ($foundRealisasi === null && str_contains($val, 'realisasi')) {
                    $foundRealisasi = $colIndex;
                    continue;
                }

                if (str_contains($val, 'sisa') && str_contains($val, 'kuota')) {
                    // Bedakan kolom "Sisa Kuota (%)" vs "Sisa Kuota (Rp)"
                    // dari tanda "%" pada header. Kalau tidak ada tanda "%"
                    // sama sekali, dianggap kolom Rupiah.
                    if (str_contains($val, '%')) {
                        if ($foundSisaPersen === null) {
                            $foundSisaPersen = $colIndex;
                        }
                    } else {
                        if ($foundSisaRp === null) {
                            $foundSisaRp = $colIndex;
                        }
                    }
                    continue;
                }
            }

            if ($foundVendor !== null && $foundSisaPersen !== null) {
                $vendorCol       = $foundVendor;
                $noKontrakCol    = $foundNoKontrak;
                $nilaiKontrakCol = $foundNilaiKontrak;
                $amdCol          = $foundAmd;
                $realisasiCol    = $foundRealisasi;
                $sisaRpCol       = $foundSisaRp;
                $sisaPersenCol   = $foundSisaPersen;
                $headerRowNumber = $row->row_number;
                break;
            }
        }

        // Sheet tidak punya kolom wajib (Vendor Pelaksana & Sisa Kuota %) - kosongkan
        if ($headerRowNumber === null) {
            return $kosong;
        }

        $vendors = [];
        $sisaKuotaPersen = [];
        $detailVendor = [];

        foreach ($rows as $row) {
            if ($row->row_number <= $headerRowNumber) {
                continue;
            }

            $cells = json_decode($row->row_data, true);
            if (!is_array($cells)) {
                continue;
            }

            $vendorAsli = trim((string) ($cells[$vendorCol] ?? ''));
            // TAMBAHAN: rapikan spasi ganda/berlebih pada nama vendor
            // sebelum ditampilkan (tidak mengubah data lain apa pun).
            $vendorAsli = preg_replace('/\s+/', ' ', $vendorAsli);

            // Baris tanpa nama Vendor dilewati (bukan data KR/kontrak
            // vendor yang valid untuk ditampilkan di grafik).
            if ($vendorAsli === '') {
                continue;
            }

            $noKontrak = $noKontrakCol !== null ? trim((string) ($cells[$noKontrakCol] ?? '')) : '';
            $nilaiKontrak = $nilaiKontrakCol !== null ? $this->parseNilaiRupiah($cells[$nilaiKontrakCol] ?? 0) : 0.0;
            $amd1 = $amdCol !== null ? $this->parseNilaiRupiah($cells[$amdCol] ?? 0) : 0.0;
            $realisasi = $realisasiCol !== null ? $this->parseNilaiRupiah($cells[$realisasiCol] ?? 0) : 0.0;
            $sisaRp = $sisaRpCol !== null ? $this->parseNilaiRupiah($cells[$sisaRpCol] ?? 0) : 0.0;
            $sisaPersen = $this->parseStatusPersen($cells[$sisaPersenCol] ?? 0);

            $vendors[] = $vendorAsli;
            $sisaKuotaPersen[] = $sisaPersen;

            $detailVendor[] = [
                'vendor'              => $vendorAsli,
                'no_kontrak'          => $noKontrak,
                'nilai_kontrak'       => round($nilaiKontrak, 2),
                'amd_1'               => round($amd1, 2),
                'realisasi_kr'        => round($realisasi, 2),
                'sisa_kuota_rp'       => round($sisaRp, 2),
                'sisa_kuota_persen'   => $sisaPersen,
            ];
        }

        return [
            'vendors'           => $vendors,
            'sisa_kuota_persen' => $sisaKuotaPersen,
            'detail_vendor'     => $detailVendor,
        ];
    }

    // ==========================
    // PERBAIKAN: Normalisasi nama Vendor Pelaksana sebelum dipakai sebagai
    // kunci pengelompokan/grouping di Grafik Progress per Vendor, supaya
    // vendor yang sebenarnya sama TIDAK dianggap sebagai 2 vendor berbeda
    // hanya karena perbedaan huruf besar/kecil (mis. "PT Sinar Kumbang
    // Sakti" vs "PT SINAR KUMBANG SAKTI") atau spasi berlebih/ganda
    // (mis. "PT  Sinar   Kumbang Sakti"). Hasil fungsi ini HANYA dipakai
    // sebagai kunci internal - nama yang ditampilkan ke user tetap memakai
    // nama asli (lihat $vendorLabel di getProgressChartData()).
    // ==========================
    private function normalizeVendorName(string $vendorName): string
    {
        $name = trim($vendorName);
        // Rapikan spasi ganda/berlebih (termasuk tab/newline nyasar) jadi 1 spasi
        $name = preg_replace('/\s+/', ' ', $name);
        // Kunci pembanding pakai huruf besar semua supaya case-insensitive
        return mb_strtoupper($name);
    }

    // ==========================
    // PERBAIKAN: Kunci pencocokan nama Vendor/Penyedia yang dipakai untuk
    // MENGHUBUNGKAN nama vendor (mis. di "DETAIL KR 2026", "KUOTA VENDOR",
    // dan sheet lain) ke halaman detail vendor (sheet RES) miliknya, lewat
    // $resVendorMap. Dibuat public static supaya bisa dipanggil langsung
    // dari view (sheet.blade.php, sheet_res.blade.php) memakai kunci yang
    // SAMA PERSIS dengan yang dipakai saat $resVendorMap dibangun di
    // buildResVendorSheetMap(), supaya nama vendor yang sebenarnya sama
    // tetap dikenali walau ada perbedaan kecil seperti huruf besar/kecil,
    // spasi berlebih, atau tanda titik (mis. "PT. Dunia Jaya Elektro" vs
    // "PT Dunia Jaya Elektro" vs "pt dunia jaya elektro").
    //
    // TIDAK mengubah normalizeVendorName() yang sudah dipakai untuk Grafik
    // Progress per Vendor, supaya perilaku chart yang sudah berjalan tidak
    // berubah.
    // ==========================
    public static function normalizeVendorKey(string $vendorName): string
    {
        $name = trim($vendorName);
        // Buang tanda titik (mis. "PT." -> "PT", "CV." -> "CV") supaya
        // singkatan dengan/tanpa titik dianggap sama.
        $name = str_replace('.', '', $name);
        // Rapikan spasi ganda/berlebih (termasuk tab/newline nyasar) jadi 1 spasi
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);
        // Kunci pembanding pakai huruf besar semua supaya case-insensitive
        return mb_strtoupper($name);
    }

    // ==========================
    // TAMBAHAN: Ubah teks nilai kontrak (mis. "Rp500.000.000", "500.000.000",
    // "500000000,50") menjadi angka murni (float) untuk ditampilkan di grafik.
    // Tidak menggunakan data dummy - murni mem-parsing isi kolom aslinya.
    // ==========================
    private function parseNilaiRupiah($value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = trim((string) $value);
        if ($str === '') {
            return 0.0;
        }

        // Buang semua karakter selain digit, koma, titik, dan minus
        // (mis. "Rp", spasi, dsb).
        $str = preg_replace('/[^0-9,.\-]/', '', $str);
        if ($str === '' || $str === '-') {
            return 0.0;
        }

        $hasComma = str_contains($str, ',');
        $hasDot   = str_contains($str, '.');

        // PERBAIKAN BUG: Sebelumnya kolom "Nilai Kontrak" yang berupa ANGKA
        // murni di Excel (bukan teks) disimpan saat upload dalam bentuk teks
        // terformat sesuai format sel aslinya (lihat readWorksheetToArray() /
        // NumberFormat::toFormattedString() di InputDataController). Untuk
        // sel dengan format sel "#,##0", hasilnya memakai KOMA sebagai
        // pemisah RIBUAN ala Excel/US (mis. 107019596 -> "107,019,596"),
        // BUKAN pemisah desimal. Kode lama selalu menganggap koma sebagai
        // pemisah desimal kalau titik tidak ada sekaligus, sehingga
        // "107,019,596" salah diubah jadi "107.019.596" (bukan angka valid)
        // dan berakhir dibaca sebagai Rp0. Logika di bawah ini mengenali
        // pola pemisah ribuan (grup 3 digit) untuk KOMA MAUPUN TITIK, jadi
        // kedua format (teks manual ala Indonesia "17.525.906" ATAUPUN
        // angka yang diformat Excel "107,019,596") sama-sama terbaca benar.
        if ($hasComma && $hasDot) {
            // Kedua simbol muncul -> simbol yang posisinya PALING TERAKHIR
            // (paling kanan) dianggap pemisah desimal, simbol lainnya
            // dianggap pemisah ribuan. Menangani format Indonesia
            // "1.500.000,50" MAUPUN format Excel/US "1,500,000.50".
            $posKomaTerakhir = strrpos($str, ',');
            $posTitikTerakhir = strrpos($str, '.');

            if ($posKomaTerakhir > $posTitikTerakhir) {
                // koma = desimal, titik = ribuan
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // titik = desimal, koma = ribuan
                $str = str_replace(',', '', $str);
            }
        } elseif ($hasComma) {
            // Hanya ada koma. Kalau polanya persis pemisah ribuan (grup 3
            // digit, mis. "107,019,596" hasil format Excel "#,##0"), buang
            // semua koma. Kalau tidak (mis. "1500000,5"), anggap koma
            // sebagai pemisah desimal (input teks manual ala Indonesia).
            if (preg_match('/^-?\d{1,3}(,\d{3})+$/', $str)) {
                $str = str_replace(',', '', $str);
            } else {
                $str = str_replace(',', '.', $str);
            }
        } elseif ($hasDot) {
            // Hanya ada titik. Kalau polanya persis pemisah ribuan (grup 3
            // digit, mis. "500.000.000"/"17.525.906"), buang semua titik.
            // Kalau tidak (mis. "1500000.5"), biarkan sebagai titik desimal.
            if (substr_count($str, '.') > 1 || preg_match('/^-?\d{1,3}(\.\d{3})+$/', $str)) {
                $str = str_replace('.', '', $str);
            }
        }

        return is_numeric($str) ? (float) $str : 0.0;
    }

    // ==========================
    // TAMBAHAN: Ubah isi kolom "Status (%)" (mis. "100%", "0", "45,5%",
    // atau pecahan seperti "0.45") menjadi angka persen murni (0-100).
    // Tidak menggunakan data dummy - murni mem-parsing isi kolom aslinya.
    // ==========================
    private function parseStatusPersen($value): float
    {
        if (is_int($value) || is_float($value)) {
            $num = (float) $value;
            // Kalau nilainya pecahan desimal 0-1 (mis. 0.45 dari format Excel
            // "Percentage"), anggap itu representasi persen dan kalikan 100.
            if ($num > 0 && $num <= 1) {
                $num *= 100;
            }
            return round($num, 2);
        }

        $str = trim((string) $value);
        if ($str === '') {
            return 0.0;
        }

        $adaTandaPersen = str_contains($str, '%');
        $str = str_replace('%', '', $str);
        $str = trim($str);
        $str = str_replace(',', '.', $str);

        if (!is_numeric($str)) {
            return 0.0;
        }

        $num = (float) $str;
        if (!$adaTandaPersen && $num > 0 && $num <= 1) {
            $num *= 100;
        }

        return round($num, 2);
    }

    // ==========================
    // TAMBAHAN: Cari ID sheet tujuan navigasi (mis. "RES MFS", "Detail KR 2026")
    // berdasarkan pola nama sheet, langsung dari database (bukan data statis).
    // Kalau $preferTahun diisi, diutamakan sheet dengan tahun/nama yang cocok;
    // kalau tidak ada, tetap fallback ke sheet pertama yang namanya cocok.
    // ==========================
    private function findSheetIdByNama(string $needle, ?string $preferTahun = null): ?int
    {
        $sheets = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%' . strtolower($needle) . '%'])
            ->orderBy('urutan')
            ->get();

        if ($sheets->isEmpty()) {
            return null;
        }

        if ($preferTahun) {
            $preferred = $sheets->first(function ($s) use ($preferTahun) {
                return (string) $s->tahun === $preferTahun || str_contains((string) $s->nama_sheet, $preferTahun);
            });

            if ($preferred) {
                return $preferred->id;
            }
        }

        return $sheets->first()->id;
    }

    // ==========================
    // TAMBAHAN: Daftar Nama Pelanggan untuk navigasi Dashboard
    // Sumber: sheet "Detail KR" (diutamakan tahun 2026), kolom "Nama Pelanggan".
    // Setiap nama disimpan berpasangan dengan ID sheet asalnya, supaya klik
    // langsung tahu mau membuka sheet mana.
    // ==========================
    private function getNamaPelangganList(): array
    {
        $sheets = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%detail kr%'])->get();

        // Utamakan sheet Detail KR tahun 2026 kalau ada
        $sheets2026 = $sheets->filter(function ($s) {
            return (string) $s->tahun === '2026' || str_contains((string) $s->nama_sheet, '2026');
        });

        if ($sheets2026->isNotEmpty()) {
            $sheets = $sheets2026;
        }

        $result = []; // 'Nama Pelanggan' => sheet_id

        foreach ($sheets as $sheet) {

            $rows = SheetData::where('sheet_id', $sheet->id)
                ->orderBy('row_number')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            // Cari baris header yang punya kolom "Nama Pelanggan"
            $pelangganCol = null;
            $headerRowNumber = null;

            foreach ($rows->take(10) as $row) {
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                foreach ($cells as $colIndex => $cellValue) {
                    $val = strtolower(trim((string) $cellValue));
                    if ($val !== '' && str_contains($val, 'pelanggan')) {
                        $pelangganCol = $colIndex;
                        break;
                    }
                }

                if ($pelangganCol !== null) {
                    $headerRowNumber = $row->row_number;
                    break;
                }
            }

            if ($headerRowNumber === null) {
                continue;
            }

            foreach ($rows as $row) {
                if ($row->row_number <= $headerRowNumber) {
                    continue;
                }

                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                $nama = trim((string) ($cells[$pelangganCol] ?? ''));
                if ($nama === '') {
                    continue;
                }

                if (!isset($result[$nama])) {
                    $result[$nama] = $sheet->id;
                }
            }
        }

        ksort($result);

        return $result;
    }

    // ==========================
    // Dashboard Admin
    // ==========================
    public function adminIndex()
{
    // Cek login admin
    if (!session()->has('admin')) {
        return redirect()->route('login.view');
    }

    // ==========================
    // TAMBAHAN: Dashboard Admin disederhanakan menjadi 3 informasi utama
    // saja (Total Data Monitoring, Total Vendor, Update Terakhir). Semua
    // angka diambil langsung dari database, BUKAN angka contoh/dummy.
    // ==========================

    // Total Data Monitoring = total baris data monitoring (KHS, Tiang,
    // Pelanggan) yang sudah tersimpan dari seluruh sheet yang diupload.
    $totalDataMonitoring = SheetData::count();

    // Total Vendor = jumlah vendor unik dari data monitoring (sheet
    // "Detail KR 2026"), dihitung dengan logika yang SAMA dengan Grafik
    // Progress per Vendor di Dashboard User (getProgressChartData()),
    // supaya angkanya konsisten dengan data monitoring yang sebenarnya.
    $progressChart = $this->getProgressChartData();
    $totalVendor = count($progressChart['vendors']);

    // ==========================
    // PERBAIKAN: Update Terakhir = waktu AKSI UPLOAD/IMPORT TERAKHIR yang
    // benar-benar BERHASIL, bukan lagi diambil dari tabel "sheet".
    //
    // Kenapa pindah dari Sheet::max('created_at') ke Upload::max('created_at'):
    // - Tabel "uploads" berisi TEPAT SATU baris untuk setiap AKSI upload/
    //   import (kolom created_at diisi sekali di awal proses, lihat
    //   InputDataController::importExcel()), sedangkan tabel "sheet" bisa
    //   berisi banyak baris untuk SATU upload yang sama (satu per worksheet
    //   Excel, ditulis satu-per-satu di dalam loop) - sehingga waktunya
    //   bisa sedikit berbeda-beda dan tidak murni mewakili "waktu upload".
    // - Baris di "uploads" HANYA tersimpan permanen kalau proses upload
    //   benar-benar berhasil sampai selesai: baris ini dibuat di dalam
    //   DB::beginTransaction() ... DB::commit() pada importExcel(), dan
    //   di-ROLLBACK otomatis (baris ikut terhapus/batal) kalau upload
    //   gagal (lihat blok catch -> DB::rollBack()). Membuka halaman upload,
    //   memilih file, upload gagal, atau upload dibatalkan TIDAK PERNAH
    //   membuat/mengubah baris di "uploads", jadi "Update Terakhir" hanya
    //   berubah setelah upload benar-benar berhasil.
    // - Timezone dikonversi ke Asia/Jakarta (WIB) saat ditampilkan di
    //   view (lihat dashboard-admin.blade.php), karena config('app.timezone')
    //   project ini adalah UTC - nilai created_at tersimpan dalam UTC.
    // ==========================
    $lastUpdated = Upload::max('created_at');

    return view('dashboard-admin.dashboard-admin', compact(
        'totalDataMonitoring',
        'totalVendor',
        'lastUpdated'
    ));
}

    // ==========================
    // TAMBAHAN: Live Search (Find/Search) pada Daftar Sheet
    // Mencari berdasarkan:
    // 1. Nama sheet (kolom "nama_sheet")
    // 2. Isi data pada setiap sheet (kolom "row_data")
    // Dipanggil lewat AJAX (JSON), tidak reload halaman.
    // ==========================
    public function searchSheet(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['sheets' => [], 'data' => []]);
        }

        $qLower = mb_strtolower($q);

        // ---- 1. Cari berdasarkan NAMA SHEET ----
        // TAMBAHAN: Sheet "REKAP" disembunyikan dari menu (lihat
        // buildKhsMenu()), jadi juga dikecualikan dari hasil pencarian
        // supaya tidak bisa diakses lewat jalur navigasi lain. Dicocokkan
        // PERSIS pada nama sheet supaya "REKAP CONNECTOR" dsb tetap muncul.
        $sheetMatches = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%' . $qLower . '%'])
            ->whereRaw('LOWER(TRIM(nama_sheet)) != ?', ['rekap'])
            ->orderBy('nama_sheet')
            ->limit(20)
            ->get()
            ->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'nama_sheet' => $s->nama_sheet,
                    'kategori'   => $s->kategori,
                ];
            });

        // ---- 2. Cari berdasarkan ISI DATA (baris Excel) ----
        // Catatan: pencarian pakai LIKE pada kolom row_data (JSON mentah).
        // Cukup cepat untuk skala data saat ini; kalau data sudah sangat
        // besar, sebaiknya ditambahkan full-text index di kemudian hari.
        $dataRows = SheetData::whereRaw('LOWER(row_data) LIKE ?', ['%' . $qLower . '%'])
            ->orderBy('id')
            ->limit(40)
            ->get();

        $dataResults = [];
        $seen = [];

        foreach ($dataRows as $row) {

            if (count($dataResults) >= 15) {
                break;
            }

            $sheet = Sheet::find($row->sheet_id);
            if (!$sheet) {
                continue;
            }

            // TAMBAHAN: Lewati hasil yang berasal dari sheet "REKAP" (nama
            // persis) karena sheet ini sengaja disembunyikan dari menu -
            // isi datanya tidak boleh ikut muncul di hasil pencarian.
            if (mb_strtolower(trim($sheet->nama_sheet ?? '')) === 'rekap') {
                continue;
            }

            $key = $sheet->id . '-' . $row->row_number;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            // Ambil potongan teks (snippet) sel yang cocok, agar user
            // tahu data apa persisnya yang ditemukan.
            $cells = json_decode($row->row_data, true);
            $snippet = '';

            if (is_array($cells)) {
                foreach ($cells as $val) {
                    $val = trim((string) $val);
                    if ($val !== '' && str_contains(mb_strtolower($val), $qLower)) {
                        $snippet = $val;
                        break;
                    }
                }
            }

            $dataResults[] = [
                'sheet_id'   => $sheet->id,
                'nama_sheet' => $sheet->nama_sheet,
                'row_number' => $row->row_number,
                'snippet'    => $snippet !== '' ? $snippet : 'Ditemukan pada baris ' . $row->row_number,
            ];
        }

        return response()->json([
            'sheets' => $sheetMatches,
            'data'   => $dataResults,
        ]);
    }

    // ==========================
    // Tampilkan Sheet
    // ==========================
    public function show($id)
    {
        $sheet = Sheet::findOrFail($id);

        // TAMBAHAN: Sheet "REKAP" (nama PERSIS, sudah tidak ada lagi di
        // spreadsheet sumber - permintaan user) sudah disembunyikan dari
        // Sidebar/menu (lihat buildKhsMenu()) dan dari hasil pencarian
        // (lihat searchSheet()), TAPI sebelumnya tetap bisa dibuka langsung
        // lewat URL /sheet/{id} kalau ID-nya ditebak/diketik manual. Supaya
        // halaman ini benar-benar tidak bisa diakses dari jalur mana pun,
        // akses langsung ke sheet ini di sini juga diblokir (404) - PERSIS
        // seperti sheet ini tidak pernah ada. TIDAK ada data yang dihapus
        // dari database (baris ini hanya menghentikan request sebelum
        // sampai ke query $rows/render view). Dicocokkan PERSIS pada nama
        // sheet supaya sheet lain yang namanya diawali kata "REKAP" (mis.
        // "REKAP CONNECTOR", "REKAP PEMBESIAN 2026") TIDAK ikut terblokir.
        if (mb_strtoupper(trim($sheet->nama_sheet ?? '')) === 'REKAP') {
            abort(404);
        }

        $rows = SheetData::where('sheet_id', $id)
            ->orderBy('row_number')
            ->get();

        // PERBAIKAN: Sheet tujuan navigasi klik Penyedia / Nama Pelanggan,
        // dipakai di seluruh sheet Monitoring KHS (bukan hanya Dashboard).
        // Sebelumnya klik Penyedia SELALU diarahkan ke satu sheet "RES MFS"
        // apa pun nama Penyedia yang diklik. Sekarang setiap Penyedia
        // dipetakan ke sheet RES miliknya masing-masing (mis. "RES. SDA",
        // "RES. MFS", "RES. SKS", dst) lewat $resVendorMap.
        $resVendorMap        = $this->buildResVendorSheetMap();
        $detailKr2026SheetId = $this->findSheetIdByNama('Detail KR', '2026');

        // Sheet RES
        if (strtoupper(substr($sheet->nama_sheet, 0, 3)) == 'RES') {
            return view('sheet_res', compact('sheet', 'rows', 'resVendorMap', 'detailKr2026SheetId'));
        }

        // TAMBAHAN: Sheet "PELANGGAN 2026" (Monitoring Pelanggan) dipakaikan
        // view khusus & terisolasi (sheet_pelanggan2026.blade.php) supaya
        // tampilannya bisa dibuat mengikuti persis struktur spreadsheet
        // aslinya (header berjenjang, lebar kolom, dst) TANPA menyentuh
        // template "sheet" yang dipakai bersama oleh sheet-sheet lainnya
        // (Monitoring KHS, Monitoring Tiang, Detail KR, dst). Dicocokkan
        // persis (bukan awalan/substring) supaya sheet lain seperti
        // "PELANGGAN 2025", "PER PELANGGAN 2026", atau "FILTER PELANGGAN"
        // tidak ikut terpengaruh dan tetap memakai view "sheet" seperti biasa.
        if (mb_strtoupper(trim($sheet->nama_sheet)) === 'PELANGGAN 2026') {
            return view('sheet_pelanggan2026', compact('sheet', 'rows'));
        }

        // TAMBAHAN: Sheet AMR, GANTER, COVER, PEMBESIAN (2024-2026), PENGIKAT,
        // TERMI JOINTING, dan KR PBPD tetap memakai view "sheet" yang sama
        // dengan sheet lain (TIDAK ada file blade tambahan) - hanya dikirim
        // flag $isFlatTableSheet supaya sheet.blade.php merender sheet ini
        // lewat cabang "satu tabel utuh" (tanpa panel info/blok per Vendor),
        // sementara sheet lain (Monitoring Tiang, RPB, WIKA, TA, MAXIMA,
        // Total Vendor, dst) tetap lewat cabang tampilan lama seperti semula.
        $isFlatTableSheet = $this->isFlatTableSheet($sheet->nama_sheet);

        return view('sheet', compact('sheet', 'rows', 'resVendorMap', 'detailKr2026SheetId', 'isFlatTableSheet'));
    }

    // ==========================
    // TAMBAHAN: Cek apakah sebuah sheet termasuk salah satu dari 7 sheet
    // (AMR, GANTER, COVER, PEMBESIAN, PENGIKAT, TERMI JOINTING, KR PBPD)
    // yang tampilannya harus dirender sebagai satu tabel utuh (tanpa panel
    // info/blok per Vendor). Dicocokkan lewat awalan nama sheet (sama
    // seperti pola $khsTambahan di buildKhsMenu()) supaya varian nama dengan
    // akhiran tahun (mis. "AMR 2025-2026", "PEMBESIAN 2024-2026") tetap ikut
    // cocok, tanpa mempengaruhi sheet lain yang tidak disebut.
    // ==========================
    private function isFlatTableSheet(?string $namaSheet): bool
    {
        $nama = mb_strtoupper(trim($namaSheet ?? ''));
        // Rapikan spasi ganda/aneh supaya "KHS   COVER" dsb tetap cocok.
        $nama = preg_replace('/\s+/', ' ', $nama);

        // PERBAIKAN: Sebelumnya hanya cocok kalau nama sheet PERSIS diawali
        // kata kunci (mis. "COVER ..."), sehingga sheet yang nama aslinya
        // tersimpan dengan awalan kategori "KHS " (mis. "KHS COVER
        // 2024-2026", "KHS PEMBESIAN 2024", "KHS PENGIKAT ...") tidak ikut
        // cocok dan tetap memakai tampilan lama.
        //
        // PERBAIKAN LANJUTAN: Untuk "TERMI JOINTING" dan "KR PBPD", nama
        // sheet ternyata memakai kata penghubung berbeda antar kedua kata
        // tsb (mis. "KHS TERMI & JOINTING", "TERMI-JOINTING", dst) sehingga
        // pencarian frasa PERSIS "TERMI JOINTING" tidak pernah cocok.
        // Sekarang tiap kata kunci multi-kata dicek per KATA (word-boundary)
        // secara terpisah - cocok selama SEMUA katanya ada di nama sheet,
        // di mana pun posisinya dan apa pun penghubung di antaranya (spasi,
        // "&", "-", "DAN", dst) - TANPA ikut mencocokkan sheet lain yang
        // kebetulan mengandung salah satu kata saja (mis. "DETAIL KR 2026"
        // tidak ikut cocok ke "KR PBPD" karena tidak ada kata "PBPD").
        // TAMBAHAN: "REKAP CONNECTOR" ikut ditambahkan ke daftar ini supaya
        // dirender sebagai satu tabel utuh (mengikuti struktur asli
        // spreadsheet: header bertingkat "REKAP TOTAL" -> REN/RESERVASI/
        // ISSUED/BELUM RESERVASI/BELUM GOOD ISUE), sama seperti 7 sheet
        // lain di bawah - TANPA membuat file blade/tampilan baru. Sheet
        // "REKAP" (tanpa "CONNECTOR") TIDAK ikut ke sini karena tidak
        // mengandung kata "CONNECTOR".
        $target = ['AMR', 'GANTER', 'COVER', 'PEMBESIAN', 'PENGIKAT', 'TERMI JOINTING', 'KR PBPD', 'REKAP CONNECTOR'];

        foreach ($target as $keyword) {
            $words = preg_split('/\s+/', $keyword);
            $allWordsFound = true;

            foreach ($words as $w) {
                if (!preg_match('/\b' . preg_quote($w, '/') . '\b/u', $nama)) {
                    $allWordsFound = false;
                    break;
                }
            }

            if ($allWordsFound) {
                return true;
            }
        }

        return false;
    }

    // ==========================
    // PERBAIKAN: Petakan setiap nama Penyedia (Vendor) ke ID sheet RES
    // miliknya sendiri, dibaca langsung dari database (bukan data statis).
    //
    // Setiap sheet RES (mis. "RES. MFS", "RES. SDA", "RES. SKS", dst) punya
    // baris info di bagian atas berbentuk "PELAKSANA : <Nama Penyedia>".
    // Nama Penyedia pada baris ini itu yang dipakai sebagai kunci peta,
    // karena nilainya sama persis dengan nilai kolom "Penyedia" pada
    // sheet-sheet Monitoring KHS (KHS Jasa, KHS Cover, dst).
    //
    // Sheet rekap seperti "RES. REKAP" dilewati karena bukan halaman detail
    // milik satu Penyedia tertentu.
    // ==========================
    private function buildResVendorSheetMap(): array
    {
        $sheets = Sheet::where(function ($q) {
            $q->whereRaw('LOWER(nama_sheet) LIKE ?', ['res%'])
              ->orWhereRaw('LOWER(kategori) LIKE ?', ['res%']);
        })->get();

        $map = [];

        foreach ($sheets as $sheet) {
            $namaSheetLower = mb_strtolower(trim($sheet->nama_sheet ?? ''));

            // Lewati sheet rekap (mis. "RES. REKAP") - bukan halaman per Penyedia.
            if (str_contains($namaSheetLower, 'rekap')) {
                continue;
            }

            // PERBAIKAN: Baris "PELAKSANA :" pada beberapa sheet RES ternyata
            // tidak selalu ada di 5 baris paling atas (mis. kalau di atasnya
            // ada baris judul/kop tambahan), jadi jangkauan baca diperlebar
            // supaya baris tersebut tetap ketemu.
            //
            // PERBAIKAN (root cause "PT PATHUR TEKNIK MANDIRI" tidak
            // clickable): jangkauan baca ditambah lagi dari 20 -> 40 baris,
            // karena tidak semua sheet RES punya struktur/jumlah baris kop
            // yang sama - ada yang baris "PELAKSANA/PENYEDIA :"-nya ada di
            // luar 20 baris pertama. Ini berlaku generik untuk SEMUA sheet
            // RES, bukan hardcode untuk satu vendor.
            $rows = SheetData::where('sheet_id', $sheet->id)
                ->orderBy('row_number')
                ->limit(40)
                ->get();

            // Inisialisasi di level sheet (bukan hanya di dalam loop $rows)
            // supaya tetap terdefinisi dengan benar walau sheet RES tsb tidak
            // punya baris data sama sekali (mencegah $found "nyasar" bawa
            // nilai dari sheet sebelumnya di iterasi luar).
            $found = false;

            foreach ($rows as $row) {
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) {
                    continue;
                }

                $found = false;

                foreach ($cells as $idx => $val) {
                    $labelRaw = trim((string) $val);
                    $label    = mb_strtolower($labelRaw);

                    // PERBAIKAN (root cause): sebelumnya HANYA label yang
                    // mengandung kata "pelaksana" yang dikenali. Sebagian
                    // sheet RES (di antaranya milik "PT PATHUR TEKNIK
                    // MANDIRI") ternyata memakai label "PENYEDIA :", bukan
                    // "PELAKSANA :", sehingga baris itu selalu dilewati dan
                    // vendor tersebut TIDAK PERNAH masuk ke $resVendorMap -
                    // ini yang membuat link-nya tetap hitam/tidak clickable
                    // walau normalizeVendorKey() dan mekanisme link di Blade
                    // sudah benar. Diperbaiki secara generik supaya kedua
                    // label (PELAKSANA maupun PENYEDIA) dikenali, berlaku
                    // untuk semua sheet RES.
                    if ($label === '' || (!str_contains($label, 'pelaksana') && !str_contains($label, 'penyedia'))) {
                        continue;
                    }

                    // Ambil sel berikutnya yang tidak kosong sebagai nama Penyedia.
                    $vendorName = '';
                    for ($i = $idx + 1; $i < count($cells); $i++) {
                        $next = trim((string) ($cells[$i] ?? ''));
                        if ($next !== '') {
                            $vendorName = $next;
                            break;
                        }
                    }

                    // PERBAIKAN: Kalau tidak ada nama di sel-sel setelahnya,
                    // kemungkinan formatnya "PELAKSANA : PT ..." digabung
                    // dalam SATU sel. Ambil teks setelah tanda ":" atau "-"
                    // pada sel label itu sendiri sebagai fallback. Dicek untuk
                    // kedua label (pelaksana / penyedia).
                    if ($vendorName === '' && preg_match('/(?:pelaksana|penyedia)\s*[:\-]\s*(.+)/i', $labelRaw, $m)) {
                        $vendorName = trim($m[1]);
                    }

                    if ($vendorName !== '') {
                        $key = self::normalizeVendorKey($vendorName);
                        if (!isset($map[$key])) {
                            $map[$key] = $sheet->id;
                        }
                        $found = true;

                        // DEBUG SEMENTARA: dipakai untuk menelusuri root cause
                        // vendor yang link-nya masih hitam/tidak clickable
                        // (mis. "PT PATHUR TEKNIK MANDIRI"). Aman dibiarkan
                        // (level info, hanya sekali per sheet RES per
                        // request), bisa dihapus kapan saja setelah tidak
                        // diperlukan lagi.
                        Log::info('[resVendorMap] vendor mapped', [
                            'vendor_raw'   => $vendorName,
                            'normalized'   => $key,
                            'label_found'  => $labelRaw,
                            'res_sheet'    => $sheet->nama_sheet,
                            'res_sheet_id' => $sheet->id,
                        ]);
                    }

                    if ($found) {
                        break;
                    }
                }

                if ($found) {
                    break;
                }
            }

            if (!$found && str_contains($namaSheetLower, 'pathur')) {
                // DEBUG SEMENTARA: khusus dicatat kalau sheet RES yang
                // namanya mengandung "pathur" ternyata TIDAK menemukan baris
                // label PELAKSANA/PENYEDIA sama sekali dalam 40 baris
                // pertama - berarti root cause-nya ada di struktur sheet
                // (label pakai kata lain, atau baris info-nya di luar 40
                // baris pertama), bukan di normalizeVendorKey()/Blade.
                Log::warning('[resVendorMap] sheet RES tidak menemukan label PELAKSANA/PENYEDIA', [
                    'res_sheet'    => $sheet->nama_sheet,
                    'res_sheet_id' => $sheet->id,
                ]);
            }
        }

        return $map;
    }
}