<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sheet->nama_sheet }}</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Calibri, Arial, Helvetica, sans-serif;
        }

        body{
            background:#f5f7fb;
        }

        .container{
            width:98%;
            margin:20px auto 40px auto;
        }

        /* ================= PAGE HEADER ================= */

        .page-header{
            background:#0B5EA8;
            color:#fff;
            padding:18px 25px;
            border-radius:10px;
            margin-bottom:16px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:10px;
        }

        .page-header h2{
            font-size:26px;
            line-height:1.3;
        }

        .page-header p{
            font-size:14px;
            opacity:.9;
        }

        .btn{
            background:#fff;
            color:#0B5EA8;
            text-decoration:none;
            padding:10px 20px;
            border-radius:8px;
            font-weight:bold;
            font-size:14px;
            transition:.2s;
            white-space:nowrap;
        }

        .btn:hover{
            background:#e9ecef;
        }

        .header-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }

        /* ================= BARIS ANOTASI / CATATAN DI ATAS HEADER (persis posisi Excel) ================= */

        .hdr-note{
            background:#fff;
            color:#000;
            font-weight:normal;
            text-align:left;
        }

        /* ================= TAMBAHAN: SATU BLOK DATA SHEET (mengikuti pola Excel:
           info/vendor + header + data + TOTAL/SISA), diulang per blok dengan
           jarak rapi antar blok - BUKAN satu tabel datar untuk seluruh sheet. ================= */

        .sheet-block{
            margin-bottom:30px;
        }

        .sheet-block:last-child{
            margin-bottom:0;
        }

        .sheet-block-title{
            font-weight:bold;
            font-size:16px;
            color:#0B5EA8;
            margin:4px 4px 14px 2px;
        }

        /* ================= TAMBAHAN: PANEL INFORMASI BLOK (VENDOR / NOMOR KONTRAK /
           TANGGAL / VOLUME / FUNGSI, dst) - DI LUAR TABEL, TIDAK IKUT SCROLL/FREEZE ================= */

        .sheet-info{
            background:#fff;
            border-radius:10px;
            padding:14px 20px;
            margin-bottom:14px;
            box-shadow:0 2px 8px rgba(0,0,0,.08);
            border:1px solid #e2e6ea;
        }

        .sheet-info-row{
            display:flex;
            flex-wrap:wrap;
            gap:4px 14px;
            padding:3px 0;
            font-size:13px;
            color:#222;
        }

        .sheet-info-row:not(:last-child){
            border-bottom:1px dashed #e9edf2;
        }

        .sheet-info-cell:empty{
            display:none;
        }

        .sheet-section-title{
            font-weight:bold;
            font-size:14px;
            color:#0B5EA8;
            margin:16px 2px 10px 2px;
        }

        /* ================= PERBAIKAN: PANEL INFO SHEET "RPB PER SPB"
           SEKARANG TABEL SUNGGUHAN, bukan layout flex/grid dua kolom lagi.
           Tabelnya otomatis ikut style tabel global di atas (thead th /
           tbody td / table-box) supaya sama persis dengan tabel detail
           sheet Monitoring Tiang lain - style di bawah ini cuma tambahan
           kecil khusus tabel info ini. Sheet lain TIDAK memakai class ini
           sama sekali. ================= */

        .rpb-info-table-box{
            margin-bottom:14px;
        }

        .rpb-info-th-label{
            width:220px;
        }

        .rpb-info-table td.text-left{
            text-align:left;
        }

        .rpb-value-multiline{
            white-space:pre-line;
        }

        /* ================= TABLE WRAPPER ================= */

        .table-box{
            background:#fff;
            border-radius:12px;
            overflow-x:auto;
            overflow-y:visible;
            position:relative;
            box-shadow:0 4px 15px rgba(0,0,0,.12);
            border:1px solid #000;
        }

        table{
            border-collapse:separate;
            border-spacing:0;
            table-layout:fixed;
            width:max-content;
            min-width:100%;
        }

        /* ================= TAMBAHAN: FREEZE/STICKY HEADER TABEL, BERLAKU
           GLOBAL untuk SEMUA tabel di sheet ini (KUOTA VENDOR, KHS, RPB/TA/
           MAXIMA/WIKA PER SPB, Total Vendor, Detail KR 2026, AMR/GANTER/
           COVER/PEMBESIAN/PENGIKAT/TERMI JOINTING/KR PBPD, dst) - satu
           mekanisme CSS dipakai bersama lewat selector "thead th" ini,
           bukan dibuat berbeda-beda per tabel. "top" per baris header
           (dipakai untuk header bertingkat / baris catatan sebelum header)
           diisi lewat inline style per baris di bawah, dihitung dari
           tinggi baris header yang memang sudah tetap (38px, 26px untuk
           hdr-note) supaya SATU header tidak menimpa header lainnya
           (tidak ada header ganda) dan tetap berurutan rapi saat di-scroll.
           Container ".table-box" di atas sudah overflow-x:auto sehingga
           freeze ini otomatis tetap berfungsi saat tabel di-scroll
           horizontal maupun saat halaman di-scroll ke bawah. ================= */
        thead th{
            background:#00B0F0;
            color:#fff;
            padding:8px 10px;
            height:38px;
            border-top:1px solid #000;
            border-left:1px solid #000;
            border-bottom:1px solid #000;
            text-align:center;
            font-weight:bold;
            font-size:13px;
            vertical-align:middle;
            white-space:normal;
            word-break:break-word;
            position:sticky;
            top:0;
            z-index:5;
        }

        thead th.hdr-note{
            height:26px;
            padding:4px 10px;
            font-size:12px;
        }

        thead th:last-child{
            border-right:1px solid #000;
        }

        tbody td{
            padding:6px 10px;
            border-top:1px solid #d9d9d9;
            border-left:1px solid #d9d9d9;
            font-size:13px;
            vertical-align:middle;
            background:#fff;
            white-space:normal;
            word-break:break-word;
        }

        tbody td:last-child{
            border-right:1px solid #d9d9d9;
        }

        tbody tr:last-child td{
            border-bottom:1px solid #d9d9d9;
        }

        tbody tr:nth-child(even) td{
            background:#F7FBFF;
        }

        tbody tr:hover td{
            background:#DCEEFF !important;
        }

        .text-left{ text-align:left; }
        .text-center{ text-align:center; }
        .text-right{ text-align:right; }

        /* ================= TAMBAHAN: BARIS TOTAL / SISA PER BLOK ================= */

        tr.row-total td{
            background:#eef4fb !important;
            font-weight:bold;
        }

        tr.row-sisa td{
            background:#fff4e5 !important;
            font-weight:bold;
        }

        /* ================= TAMBAHAN: SATU TABEL UTUH (AMR/GANTER/COVER/
           PEMBESIAN/PENGIKAT/TERMI JOINTING/KR PBPD) - dipakai HANYA saat
           $isFlatTableSheet true. Baris judul section (mis. "FUNGSI
           PEMASARAN") tetap sebagai baris tabel (colspan penuh), bukan div
           judul terpisah. ================= */

        tr.row-section td{
            background:#eaf6fd !important;
            color:#0B5EA8;
            font-weight:bold;
            font-size:14px;
            text-align:left;
        }

        thead th.hdr-note{
            background:#eaf6fd;
            color:#0B5EA8;
        }

        /* ================= TAMBAHAN (khusus REKAP CONNECTOR): judul kecil
           ("REKAP MATERIAL") di atas tabel, rata kiri, teks hitam - BUKAN
           bagian dari header tabel (lihat $rekapConnectorCaption di
           sheet.blade.php). Class ".rekap-connector-table" HANYA dipasang
           pada tabel REKAP CONNECTOR, jadi override warna di bawah ini
           TIDAK mempengaruhi tabel sheet flat-table lain (AMR/GANTER/
           COVER/PEMBESIAN/PENGIKAT/TERMI JOINTING/KR PBPD). ================= */

        .rekap-connector-caption{
            font-weight:bold;
            font-size:15px;
            color:#000;
            text-align:left;
            padding:2px 4px 10px 6px;
        }

        /* TAMBAHAN (revisi tampilan): baris section di REKAP CONNECTOR
           (mis. "a.  MATERIAL UTAMA :") tetap rata kiri & warna hitam,
           TAPI tidak bold lagi - hanya heading utama Romawi (lihat
           ".row-section-major" di bawah) yang boleh bold. */
        .rekap-connector-table tr.row-section td{
            background:#fff !important;
            color:#000;
            font-weight:normal;
            font-size:13px;
        }

        /* TAMBAHAN (revisi tampilan): heading utama berangka Romawi
           (I./II./III., lihat variabel $isSectionMajor di sheet.blade.php)
           - satu-satunya baris isi tabel REKAP CONNECTOR yang tetap bold. */
        .rekap-connector-table tr.row-section-major td{
            font-weight:bold;
            font-size:14px;
        }

        .rekap-connector-table tbody tr:nth-child(even) td{
            background:#fff;
        }

        /* TAMBAHAN (revisi tampilan): semua isi tabel REKAP CONNECTOR
           konsisten teks HITAM + normal weight (termasuk sel yang berisi
           link navigasi, mis. ".cell-nav-link" yang di sheet lain
           berwarna biru) - hanya heading utama (".row-section-major" di
           atas) dan header tabel (thead, sudah putih+bold secara global)
           yang berbeda. */
        .rekap-connector-table tbody td{
            color:#000 !important;
            font-weight:normal;
        }

        .rekap-connector-table tbody td a{
            color:#000 !important;
        }

        /* TAMBAHAN (revisi tampilan): kolom NO dibuat sempit & proporsional
           (bukan selebar kolom lain) - lihat override lebar kolom per index
           di colgroup sheet.blade.php ($isRekapConnectorSheet). Perataan
           tengah kolom NO sekarang diatur LANGSUNG lewat class
           "text-center" di <td> (lihat cabang $isRekapConnectorSheet di
           tbody), bukan lewat trik CSS ":first-child" lagi. */

        /* ================= EMPTY STATE ================= */

        /* ================= TAMBAHAN: TOMBOL LINK DARI EXCEL ================= */

        .cell-link-btn{
            display:inline-flex;
            align-items:center;
            gap:5px;
            padding:5px 12px;
            background:#eef4fb;
            color:#0B5EA8;
            border-radius:6px;
            font-size:12px;
            font-weight:600;
            text-decoration:none;
            white-space:nowrap;
            transition:.15s;
        }

        .cell-link-btn:hover{
            background:#0B5EA8;
            color:#fff;
        }

        /* TAMBAHAN: Link navigasi Vendor -> RES MFS / Nama Pelanggan -> Detail KR 2026 */
        .cell-nav-link{
            color:#0B5EA8;
            text-decoration:underline;
            text-decoration-style:dotted;
            text-underline-offset:2px;
        }

        .cell-nav-link:hover{
            color:#08417a;
            text-decoration-style:solid;
        }

        .empty-state{
            padding:40px;
            text-align:center;
            color:#888;
            font-size:14px;
        }

        /* ================= TAMBAHAN: HIGHLIGHT HASIL PENCARIAN ================= */

        tr.row-highlight td{
            background:#fff3b0 !important;
            transition:background 1.5s ease;
        }

        /* ================= TAMBAHAN: FILTER BANNER (Vendor/Nama Pelanggan) ================= */

        .filter-banner{
            background:#eef4fb;
            color:#0B5EA8;
            border:1px solid #cfe1f5;
            padding:10px 16px;
            border-radius:8px;
            margin-bottom:14px;
            font-size:13px;
        }

        .filter-banner a{
            color:#0B5EA8;
            font-weight:bold;
            text-decoration:underline;
        }

        /* TAMBAHAN: Warning saat vendor tidak punya sheet RES sendiri dan
           di-fallback ke DETAIL KR 2026 (lihat $vendorFallbackWarning). */
        .filter-banner-warning{
            background:#fdf3e7;
            color:#9a5b00;
            border:1px solid #f3ddb3;
        }

        .filter-banner-warning a{
            color:#9a5b00;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width:768px){
            .page-header h2{ font-size:20px; }
            .page-header p{ font-size:12px; }
            .btn{ padding:8px 14px; font-size:12px; }
            thead th, tbody td{ font-size:12px; padding:5px 7px; }
        }

    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <div>
            <h2>{{ $sheet->nama_sheet }}</h2>
            <p>Total Baris : {{ $sheet->total_rows }}</p>
        </div>

        @php
            // KOREKSI: Tombol Dashboard di sheet Monitoring Tiang ini khusus
            // untuk USER - selalu mengarah ke Dashboard User (route 'dashboard'),
            // bukan Dashboard Admin. Tidak ada percabangan role di sini.
            $dashboardRoute = route('dashboard');
        @endphp

        <div class="header-actions">
            {{-- PERBAIKAN: Tombol Back sekarang mengikuti riwayat navigasi (browser history)
                 satu langkah ke belakang - bukan selalu ke Dashboard. href tetap mengarah
                 ke Dashboard User sebagai fallback (kalau JS mati, atau halaman
                 ini dibuka langsung lewat URL tanpa riwayat sebelumnya). --}}
            <a href="{{ $dashboardRoute }}" class="btn" id="btnBackHistory">
                &larr; Kembali
            </a>

            {{-- TAMBAHAN: Tombol Dashboard - selalu langsung ke Dashboard User,
                 tanpa perlu menekan Kembali berkali-kali. --}}
            <a href="{{ $dashboardRoute }}" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px; margin-right:4px;">
                    <path d="M3 11.5 12 4l9 7.5"></path>
                    <path d="M5.5 10v9a1 1 0 0 0 1 1H9.5a1 1 0 0 0 1-1V15a1 1 0 0 1 1-1H12.5a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1H17.5a1 1 0 0 0 1-1v-9"></path>
                </svg>Dashboard
            </a>
        </div>
    </div>

    @php
        // TAMBAHAN: Filter server-side dari klik Vendor/Nama Pelanggan di Dashboard
        // PERBAIKAN: tambahkan filter "kr" (klik kolom Uraian Pekerjaan (KR) di KHS JASA 2026)
        // TAMBAHAN: filter "paket" (klik kolom PAKET pada panel Detail
        // Vendor di Dashboard) dan "lokasi" (klik kolom Detail
        // Pekerjaan/Lokasi Pekerjaan pada panel yang sama) - keduanya
        // memakai pola navigasi yang SAMA seperti vendor/pelanggan/kr di
        // atas (route sheet.show + query filter), tidak mengubah filter
        // yang sudah ada.
        $filterValue = trim((string) request('vendor', request('pelanggan', request('kr', request('paket', request('lokasi', ''))))));
        $filterLabel = request()->has('vendor')
            ? 'Penyedia'
            : (request()->has('pelanggan') ? 'Nama Pelanggan' : (request()->has('kr') ? 'Uraian Pekerjaan (KR)' : (request()->has('paket') ? 'Paket / Kontrak Rinci' : (request()->has('lokasi') ? 'Lokasi Pekerjaan / Detail Pekerjaan' : null))));
    @endphp

    @php
        // TAMBAHAN: Peringatan saat sebuah Vendor/Penyedia TIDAK memiliki
        // sheet RES sendiri sehingga link-nya di-fallback ke DETAIL KR 2026
        // (lihat buildResVendorSheetMap() & fallback link vendor di bawah).
        // Ditampilkan hanya saat sedang membuka DETAIL KR 2026 lewat filter
        // ?vendor=... dan vendor tsb memang tidak ada di $resVendorMap - tidak
        // mempengaruhi filter Nama Pelanggan/Uraian Pekerjaan (KR)/Paket/Lokasi
        // maupun tampilan sheet lain.
        $vendorFallbackWarning = null;
        if ($filterLabel === 'Penyedia' && $filterValue !== '' && $detailKr2026SheetId && (int) $sheet->id === (int) $detailKr2026SheetId) {
            $vendorKeyForWarning  = \App\Http\Controllers\DashboardController::normalizeVendorKey($filterValue);
            $mappedResSheetIdCheck = $resVendorMap[$vendorKeyForWarning] ?? null;
            if (!$mappedResSheetIdCheck || (int) $mappedResSheetIdCheck === (int) $sheet->id) {
                $vendorFallbackWarning = $filterValue;
            }
        }
    @endphp

    @if($vendorFallbackWarning)
        <div class="filter-banner filter-banner-warning">
            Data RES untuk vendor <strong>{{ $vendorFallbackWarning }}</strong> tidak ditemukan. Menampilkan data vendor dari DETAIL KR 2026.
        </div>
    @endif

    @if($filterLabel && $filterValue !== '')
        <div class="filter-banner">
            Menampilkan data untuk {{ $filterLabel }}: <strong>{{ $filterValue }}</strong>
            &nbsp;·&nbsp;
            <a href="{{ url()->current() }}">Tampilkan Semua Data</a>
        </div>
    @endif

    @if($isFlatTableSheet)
        {{--
            TAMBAHAN: Sheet AMR, GANTER, COVER, PEMBESIAN (2024-2026),
            PENGIKAT, TERMI JOINTING, dan KR PBPD dirender di sini sebagai
            SATU TABEL UTUH, persis urutan baris/kolom aslinya di Excel:

                HEADER HALAMAN
                  -> TABEL UTAMA (satu tabel, tanpa card/panel/list di luar
                     tabel - berbeda dari cabang @else di bawah yang memecah
                     sheet lain jadi beberapa "blok" per Vendor + panel info
                     terpisah).

            Baris apa pun yang sebelumnya tercecer di luar tabel (mis.
            baris ringkasan/anotasi seperti "KOMITMEN TKDN" / "TARGET
            FISIK", baris judul section seperti "FUNGSI PEMASARAN") tetap
            ditampilkan, tapi sebagai BARIS di dalam <table> ini (di posisi
            baris aslinya) - bukan sebagai elemen terpisah di luar tabel.
            Tidak ada data yang dihapus dari database; hanya cara render
            pada halaman yang diperbaiki. Sheet lain (Monitoring Tiang,
            RPB, WIKA, TA, MAXIMA, Total Vendor, Pelanggan 2026, dst) TIDAK
            lewat sini - tetap memakai cabang @else seperti semula.
        --}}
        @php
            $decode = function ($row) {
                $d = json_decode($row->row_data ?? '[]', true);
                return is_array($d) ? $d : [];
            };

            $trim = function ($v) {
                return trim((string) ($v ?? ''));
            };

            $detectLink = function ($cell) {
                if (!preg_match('#^https?://#i', $cell)) {
                    return null;
                }
                $lower = strtolower($cell);
                if (str_contains($lower, 'arcgis') || str_contains($lower, 'google.com/maps') || str_contains($lower, 'maps.google')) {
                    return ['icon' => '📍', 'label' => 'Buka Peta'];
                }
                if (str_contains($lower, 'drive.google.com') || str_contains($lower, 'docs.google.com') || str_contains($lower, 'sharepoint.com')) {
                    return ['icon' => '📄', 'label' => 'Lihat Dokumen'];
                }
                return ['icon' => '🔗', 'label' => 'Buka Link'];
            };

            // Jumlah kolom sheet = kolom terbanyak di antara SELURUH baris.
            $totalCols = 1;
            foreach ($rows as $row) {
                $totalCols = max($totalCols, count($decode($row)));
            }

            // TAMBAHAN: Dipakai HANYA untuk penyesuaian kecil (deteksi baris
            // judul section/kelompok material) yang khusus dibutuhkan oleh
            // REKAP CONNECTOR - lihat penjelasan $isSectionTitle di bawah.
            // Sheet flat-table lain (AMR, GANTER, dst) TIDAK terpengaruh.
            $isRekapConnectorSheet = mb_strtoupper($trim($sheet->nama_sheet ?? '')) === 'REKAP CONNECTOR';

            // TAMBAHAN (khusus REKAP PEMBESIAN 2026): pada sheet ini header
            // "MATERIAL" tersimpan DUA KALI berdampingan (kolom B & C
            // sama-sama berjudul "MATERIAL") - begitu adanya di file Excel
            // asal & di database (SheetData/row_data), BUKAN hasil bug
            // parsing di aplikasi ini. Isi kedua kolom itu TERNYATA TIDAK
            // selalu identik (sebagian besar baris sama persis, tapi ada
            // beberapa baris nama materialnya sedikit berbeda antara kolom
            // B & C) - karena itu kolom kedua TIDAK asal dibuang begitu
            // saja (supaya tidak ada data yang hilang), melainkan ISINYA
            // DIGABUNG ke kolom MATERIAL pertama (lihat blok penggabungan
            // nilai di bawah, dekat pembentukan $allRowsFlat) baru
            // kolomnya disatukan jadi SATU kolom tampilan. $rows (data
            // asli dari database) TIDAK diubah/dihapus sama sekali - hanya
            // salinan sel per baris yang dipakai untuk merender tabel di
            // sini yang disesuaikan. Dicocokkan PERSIS ke nama sheet
            // "REKAP PEMBESIAN 2026" supaya sheet flat-table lain (AMR,
            // GANTER, COVER, PENGIKAT, TERMI JOINTING, KR PBPD, REKAP
            // CONNECTOR) dan logic umum ($buildGroups, dst) TIDAK
            // terpengaruh sama sekali.
            $isRekapPembesian2026Sheet = mb_strtoupper($trim($sheet->nama_sheet ?? '')) === 'REKAP PEMBESIAN 2026';

            $dupMaterialColIdxKeep = null;
            $dupMaterialColIdxDrop = null;
            if ($isRekapPembesian2026Sheet) {
                foreach ($rows as $row) {
                    $cells = $decode($row);
                    for ($ci = 0; $ci < count($cells) - 1; $ci++) {
                        $curLabel  = mb_strtoupper($trim($cells[$ci] ?? ''));
                        $nextLabel = mb_strtoupper($trim($cells[$ci + 1] ?? ''));
                        if ($curLabel === 'MATERIAL' && $nextLabel === 'MATERIAL') {
                            $dupMaterialColIdxKeep = $ci;
                            $dupMaterialColIdxDrop = $ci + 1;
                            break 2;
                        }
                    }
                }

                if ($dupMaterialColIdxDrop !== null && $totalCols > 1) {
                    $totalCols--;
                }
            }

            $isBlankCells = function ($cells) use ($trim) {
                foreach ($cells as $v) {
                    if ($trim($v) !== '') {
                        return false;
                    }
                }
                return true;
            };

            // Baris dianggap HEADER KOLOM TABEL kalau mayoritas sel terisi
            // berupa teks (bukan angka) dan cukup banyak sel yang terisi.
            $isHeaderCandidate = function ($cells) use ($totalCols, $trim) {
                $filled = 0;
                $textCount = 0;
                foreach ($cells as $v) {
                    $t = $trim($v);
                    if ($t !== '') {
                        $filled++;
                        if (!is_numeric($t)) {
                            $textCount++;
                        }
                    }
                }
                $ratio     = $totalCols > 0 ? ($filled / $totalCols) : 0;
                $textRatio = $filled > 0 ? ($textCount / $filled) : 0;

                return $filled >= 3 && $ratio >= 0.4 && $textRatio >= 0.5;
            };

            // Kelompokkan sel yang di Excel-nya merge (sekarang jadi sel
            // kosong berturut-turut) supaya tampil menyatu (colspan), TANPA
            // mengubah isi.
            $buildGroups = function ($cells) use ($totalCols, $trim) {
                $groups = [];
                $c = 0;
                while ($c < $totalCols) {
                    $label = $trim($cells[$c] ?? '');
                    $width = 1;
                    $j = $c + 1;
                    while ($j < $totalCols && $trim($cells[$j] ?? '') === '') {
                        $width++;
                        $j++;
                    }
                    $groups[] = ['start' => $c, 'width' => $width, 'label' => $label];
                    $c += $width;
                }
                return $groups;
            };

            $widthFor = function ($label) {
                $l = mb_strtoupper(trim($label));
                if ($l === '') return 110;
                if ($l === 'NO') return 64;
                if (str_contains($l, 'LOKASI')) return 340;
                if (str_contains($l, 'KETERANGAN') || str_contains($l, 'KENDALA')) return 340;
                if (str_contains($l, 'VENDOR') || str_contains($l, 'PENYEDIA')) return 230;
                if ($l === 'UNIT') return 190;
                if (str_contains($l, 'MATERIAL')) return 340;
                if (str_contains($l, 'TGL') || str_contains($l, 'TANGGAL')) return 150;
                if (str_contains($l, 'WO')) return 180;
                if (str_contains($l, 'PRIORITAS')) return 130;
                if (str_contains($l, 'FUNGSI')) return 150;
                if (str_contains($l, 'PROGRES') || str_contains($l, 'PROGRESS')) return 170;
                if (str_contains($l, 'GAMBAR')) return 120;
                if (str_contains($l, 'REKON')) return 140;
                if (preg_match('/\d\s*[\/\-]\s*\d/', $l)) return 120;
                if (str_contains($l, 'JUMLAH') || $l === 'TOTAL') return 120;
                if (str_contains($l, 'KR')) return 160;
                return 150;
            };

            $allRowsFlat = [];
            foreach ($rows as $row) {
                $cells = $decode($row);
                if ($isBlankCells($cells)) continue; // baris kosong dilewati, bukan pemisah blok

                // TAMBAHAN (khusus REKAP PEMBESIAN 2026): satukan kolom
                // "MATERIAL" kedua yang duplikat ke kolom MATERIAL pertama
                // SEBELUM kolom keduanya disatukan/dibuang dari tampilan -
                // supaya TIDAK ADA data material yang hilang walau kedua
                // kolom digabung jadi satu tampilan. Array $cells di sini
                // adalah salinan lokal untuk keperluan render saja,
                // $row->row_data asli di database tidak disentuh sama
                // sekali.
                if ($dupMaterialColIdxDrop !== null && array_key_exists($dupMaterialColIdxDrop, $cells)) {
                    $keepVal = $trim($cells[$dupMaterialColIdxKeep] ?? '');
                    $dropVal = $trim($cells[$dupMaterialColIdxDrop] ?? '');

                    if ($dropVal === '' || $dropVal === $keepVal) {
                        // Sama persis (kasus paling umum) atau kolom kedua
                        // kosong -> cukup pakai nilai kolom pertama.
                        $mergedVal = $keepVal;
                    } elseif ($keepVal === '') {
                        $mergedVal = $dropVal;
                    } elseif (str_contains($dropVal, $keepVal)) {
                        // Kolom kedua adalah versi LEBIH LENGKAP dari kolom
                        // pertama (mis. ada keterangan tambahan) -> pakai
                        // yang lebih lengkap supaya tidak ada info hilang.
                        $mergedVal = $dropVal;
                    } elseif (str_contains($keepVal, $dropVal)) {
                        $mergedVal = $keepVal;
                    } else {
                        // Isi kedua kolom benar-benar berbeda (bukan cuma
                        // beda panjang teks) -> gabungkan KEDUANYA supaya
                        // datanya tetap utuh, tidak ada yang terbuang.
                        $mergedVal = $keepVal . ' / ' . $dropVal;
                    }

                    $cells[$dupMaterialColIdxKeep] = $mergedVal;
                    array_splice($cells, $dupMaterialColIdxDrop, 1);
                }

                $allRowsFlat[] = ['number' => $row->row_number, 'cells' => $cells];
            }

            // Cari SATU baris header kolom (dalam 15 baris pertama yang
            // berisi data) - sisanya (sebelum & sesudah) tetap masuk
            // sebagai baris tabel, BUKAN dibuang atau dijadikan panel/card
            // terpisah.
            $headerIdx = null;
            $scanLimit = min(15, count($allRowsFlat));
            for ($i = 0; $i < $scanLimit; $i++) {
                if ($isHeaderCandidate($allRowsFlat[$i]['cells'])) {
                    $headerIdx = $i;
                    break;
                }
            }

            $noteRows     = [];
            $headerRowF   = null;
            $subHeaderRowF = null;
            $dataStartF   = 0;

            if ($headerIdx !== null) {
                $noteRows   = array_slice($allRowsFlat, 0, $headerIdx);
                $headerRowF = $allRowsFlat[$headerIdx]['cells'];
                $dataStartF = $headerIdx + 1;

                // Cek header bertingkat (mis. KEBUTUHAN -> 9/200, 12/200, JUMLAH).
                $groupsF      = $buildGroups($headerRowF);
                $groupedColsF = array_filter($groupsF, fn ($g) => $g['width'] > 1);

                if (!empty($groupedColsF) && isset($allRowsFlat[$headerIdx + 1])) {
                    $next = $allRowsFlat[$headerIdx + 1]['cells'];
                    $ok   = true;

                    foreach ($groupsF as $g) {
                        $slice  = array_slice($next, $g['start'], $g['width']);
                        $filled = false;
                        foreach ($slice as $v) {
                            if ($trim($v) !== '') { $filled = true; break; }
                        }
                        if ($g['width'] === 1 && $filled) { $ok = false; break; }
                        if ($g['width'] > 1 && !$filled)  { $ok = false; break; }
                    }

                    if ($ok) {
                        $subHeaderRowF = $next;
                        $dataStartF    = $headerIdx + 2;
                    }
                }
            }

            $dataRowsF = $headerRowF !== null ? array_slice($allRowsFlat, $dataStartF) : $allRowsFlat;

            // TAMBAHAN (khusus REKAP CONNECTOR): baris catatan sebelum
            // header (mis. "REKAP MATERIAL") dikeluarkan dari struktur
            // <thead> supaya blok header NO/MATERIAL MDU/REKAP TOTAL
            // benar-benar menyatu jadi SATU blok biru tanpa ada baris
            // terpisah di atasnya. Judulnya tetap ditampilkan, tapi sebagai
            // teks kecil di atas tabel (rata kiri), bukan sebagai baris
            // header. HANYA berlaku untuk REKAP CONNECTOR - sheet flat-table
            // lain (AMR/GANTER/COVER/PEMBESIAN/PENGIKAT/TERMI JOINTING/KR
            // PBPD) tetap memakai $noteRows di dalam <thead> seperti semula
            // (tidak berubah sama sekali).
            $rekapConnectorCaption = null;
            if ($isRekapConnectorSheet && !empty($noteRows)) {
                foreach ($noteRows as $nr) {
                    foreach ($buildGroups($nr['cells']) as $g) {
                        if ($g['label'] !== '') {
                            $rekapConnectorCaption = $g['label'];
                            break 2;
                        }
                    }
                }
                $noteRows = [];
            }

            // Label kolom (dipakai untuk lebar kolom proporsional).
            $colLabelF = array_fill(0, $totalCols, '');
            if ($headerRowF !== null) {
                $lastMain = '';
                for ($c = 0; $c < $totalCols; $c++) {
                    $sub  = $subHeaderRowF ? $trim($subHeaderRowF[$c] ?? '') : '';
                    $main = $trim($headerRowF[$c] ?? '');
                    if ($main !== '') { $lastMain = $main; }
                    $colLabelF[$c] = $sub !== '' ? $sub : ($main !== '' ? $main : $lastMain);
                }
            }

            // Deteksi kolom Vendor/Penyedia, Nama Pelanggan, Uraian
            // Pekerjaan (KR)/Paket, dan Lokasi Pekerjaan/Detail Pekerjaan -
            // dipakai untuk link klik-navigasi (fitur yang sudah ada
            // sebelumnya, dipertahankan di sini).
            $vendorColF    = null;
            $pelangganColF = null;
            $uraianKrColF  = null;
            $paketColF     = null;
            $lokasiColF    = null;

            foreach ([$headerRowF, $subHeaderRowF] as $hrow) {
                if (!$hrow) continue;
                foreach ($hrow as $colIdx => $headerVal) {
                    $headerVal = mb_strtolower($trim($headerVal));
                    if ($headerVal === '') continue;
                    if ($vendorColF === null && (str_contains($headerVal, 'vendor') || str_contains($headerVal, 'penyedia'))) {
                        $vendorColF = $colIdx;
                    }
                    if ($pelangganColF === null && str_contains($headerVal, 'pelanggan')) {
                        $pelangganColF = $colIdx;
                    }
                    if ($uraianKrColF === null && str_contains($headerVal, 'uraian pekerjaan')) {
                        $uraianKrColF = $colIdx;
                    }
                    if ($paketColF === null && str_contains($headerVal, 'paket')) {
                        $paketColF = $colIdx;
                    }
                    if ($lokasiColF === null && str_contains($headerVal, 'lokasi')) {
                        $lokasiColF = $colIdx;
                    }
                }
            }

            // PERBAIKAN: Vendor/Penyedia yang TIDAK memiliki sheet RES sendiri
            // tetap harus clickable (fallback ke DETAIL KR 2026 dengan filter
            // vendor - lihat blok $navLink di bawah), bukan hanya vendor yang
            // sudah ada di $resVendorMap.
            $canLinkVendorF    = $vendorColF !== null && (!empty($resVendorMap) || ($detailKr2026SheetId && (int) $detailKr2026SheetId !== (int) $sheet->id));
            $canLinkPelangganF = $pelangganColF !== null && $detailKr2026SheetId && $detailKr2026SheetId != $sheet->id;
            $canLinkUraianKrF  = $uraianKrColF !== null && $paketColF !== null
                && $detailKr2026SheetId && $detailKr2026SheetId != $sheet->id;

            $filterColF = null;
            if ($filterValue !== '') {
                $filterColF = $filterLabel === 'Penyedia'
                    ? $vendorColF
                    : ($filterLabel === 'Nama Pelanggan' ? $pelangganColF : (in_array($filterLabel, ['Uraian Pekerjaan (KR)', 'Paket / Kontrak Rinci'], true) ? $paketColF : ($filterLabel === 'Lokasi Pekerjaan / Detail Pekerjaan' ? $lokasiColF : null)));
            }

            if ($filterColF !== null) {
                $dataRowsF = array_values(array_filter($dataRowsF, function ($r) use ($filterColF, $trim, $filterValue) {
                    return mb_strtolower($trim($r['cells'][$filterColF] ?? '')) === mb_strtolower($filterValue);
                }));
            }
        @endphp

        @if(empty($allRowsFlat))
            <div class="table-box">
                <div class="empty-state">Tidak ada data untuk ditampilkan.</div>
            </div>
        @else
            @if($isRekapConnectorSheet && $rekapConnectorCaption)
                <div class="rekap-connector-caption">{{ $rekapConnectorCaption }}</div>
            @endif
            <div class="table-box{{ $isRekapConnectorSheet ? ' rekap-connector-table' : '' }}">
                <table>
                    <colgroup>
                        @for($c = 0; $c < $totalCols; $c++)
                            @php
                                // TAMBAHAN (khusus REKAP CONNECTOR - revisi tampilan):
                                // proporsi lebar kolom disesuaikan supaya mirip
                                // spreadsheet referensi - NO sempit (kolom ke-0),
                                // MATERIAL MDU paling lebar (kolom ke-1), lalu 5
                                // kolom REKAP TOTAL (REN/RESERVASI/ISSUED/BELUM
                                // RESERVASI/BELUM GOOD ISUE) proporsional sama
                                // besar. HANYA berlaku untuk sheet REKAP CONNECTOR -
                                // $widthFor() default di atas TIDAK diubah sama
                                // sekali, jadi sheet flat-table lain (AMR, GANTER,
                                // COVER, PEMBESIAN, PENGIKAT, TERMI JOINTING, KR
                                // PBPD) tetap memakai lebar seperti semula.
                                $colWidthPxF = $widthFor($colLabelF[$c]);
                                if ($isRekapConnectorSheet) {
                                    if ($c === 0) {
                                        $colWidthPxF = 48;
                                    } elseif ($c === 1) {
                                        $colWidthPxF = 320;
                                    } else {
                                        $colWidthPxF = 120;
                                    }
                                }
                            @endphp
                            <col style="width:{{ $colWidthPxF }}px;">
                        @endfor
                    </colgroup>

                    <thead>
                        @if($isRekapConnectorSheet)
                            {{--
                                TAMBAHAN (khusus REKAP CONNECTOR - rendering EKSPLISIT,
                                revisi total): sebelumnya header sheet ini dibangun
                                lewat heuristik umum ($buildGroups($headerRowF),
                                $mainGroupsF, $subHeaderRowF) yang dipakai bersama utk
                                SEMUA sheet flat-table. Heuristik itu terbukti masih
                                bisa salah untuk sheet ini (label kosong / posisi
                                meleset), jadi KHUSUS REKAP CONNECTOR headernya
                                sekarang ditulis EKSPLISIT & TETAP (bukan hasil deteksi
                                otomatis), karena strukturnya memang selalu sama:

                                    NO (rowspan 2) | MATERIAL MDU (rowspan 2) | REKAP TOTAL (colspan N)
                                                                                 REN | RESERVASI | ISSUED | BELUM RESERVASI | BELUM GOOD ISUE

                                $rekapTotalSpanF dihitung dari $totalCols (jumlah
                                kolom asli dari data, TIDAK di-hardcode) supaya tetap
                                mengikuti jumlah kolom REKAP TOTAL yang sebenarnya ada
                                di data - hanya NAMA kolomnya yang tetap (5 nama sudah
                                pasti/tidak pernah berubah di sheet ini). Sheet lain
                                (AMR/GANTER/COVER/PEMBESIAN/PENGIKAT/TERMI JOINTING/KR
                                PBPD) TIDAK lewat sini sama sekali - cabang @else di
                                bawah (logic lama) tidak diubah satu baris pun.
                            --}}
                            @php
                                $rekapSubHeadersF = ['REN', 'RESERVASI', 'ISSUED', 'BELUM RESERVASI', 'BELUM GOOD ISUE'];
                                $rekapTotalSpanF  = max($totalCols - 2, 1);
                            @endphp
                            <tr>
                                <th style="top:0px;" rowspan="2">NO</th>
                                <th style="top:0px;" rowspan="2">MATERIAL MDU</th>
                                <th style="top:0px;" colspan="{{ $rekapTotalSpanF }}">REKAP TOTAL</th>
                            </tr>
                            <tr>
                                @for($si = 0; $si < $rekapTotalSpanF; $si++)
                                    <th style="top:38px;">{{ $rekapSubHeadersF[$si] ?? ('KOL '.($si + 1)) }}</th>
                                @endfor
                            </tr>
                        @else
                            @foreach($noteRows as $rIdx => $r)
                                <tr>
                                    @foreach($buildGroups($r['cells']) as $g)
                                        <th class="hdr-note" style="top:{{ $rIdx * 26 }}px;" colspan="{{ $g['width'] }}">{!! $g['label'] !== '' ? e($g['label']) : '&nbsp;' !!}</th>
                                    @endforeach
                                </tr>
                            @endforeach

                            @if($headerRowF)
                                @php
                                    $mainGroupsF = $buildGroups($headerRowF);
                                    // TAMBAHAN: posisi "top" sticky baris header utama =
                                    // total tinggi seluruh baris catatan (hdr-note) di
                                    // atasnya, supaya tidak menimpa/tertimpa saat scroll.
                                    $mainHeaderTopF = count($noteRows) * 26;
                                @endphp
                                <tr>
                                    @foreach($mainGroupsF as $g)
                                        <th style="top:{{ $mainHeaderTopF }}px;" colspan="{{ $g['width'] }}" @if($g['width'] === 1 && $subHeaderRowF) rowspan="2" @endif>
                                            {!! $g['label'] !== '' ? e($g['label']) : '&nbsp;' !!}
                                        </th>
                                    @endforeach
                                </tr>

                                @if($subHeaderRowF)
                                    @php $subHeaderTopF = $mainHeaderTopF + 38; @endphp
                                    <tr>
                                        @foreach($mainGroupsF as $g)
                                            @if($g['width'] > 1)
                                                @php
                                                    $subGroupsF = [];
                                                    $sc = $g['start'];
                                                    $send = $g['start'] + $g['width'];
                                                    while ($sc < $send) {
                                                        $slabel = $trim($subHeaderRowF[$sc] ?? '');
                                                        $sw = 1;
                                                        $sj = $sc + 1;
                                                        while ($sj < $send && $trim($subHeaderRowF[$sj] ?? '') === '') {
                                                            $sw++; $sj++;
                                                        }
                                                        $subGroupsF[] = ['width' => $sw, 'label' => $slabel];
                                                        $sc += $sw;
                                                    }
                                                @endphp
                                                @foreach($subGroupsF as $sg)
                                                    <th style="top:{{ $subHeaderTopF }}px;" colspan="{{ $sg['width'] }}">{!! $sg['label'] !== '' ? e($sg['label']) : '&nbsp;' !!}</th>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tr>
                                @endif
                            @endif
                        @endif
                    </thead>

                    <tbody>
                        @forelse($dataRowsF as $r)
                            @if($isRekapConnectorSheet)
                                {{--
                                    TAMBAHAN (khusus REKAP CONNECTOR - rendering
                                    EKSPLISIT, revisi total): posisi kolom TIDAK lagi
                                    ditentukan lewat $buildGroups($data) /
                                    $isSectionTitle (heuristik umum yang dipakai sheet
                                    flat-table lain). Untuk sheet ini, mapping kolom
                                    SELALU TETAP:

                                        index 0 = NO
                                        index 1 = MATERIAL MDU
                                        index 2..N = REN / RESERVASI / ISSUED /
                                                     BELUM RESERVASI / BELUM GOOD ISUE

                                    Baris DATA MATERIAL sungguhan dikenali murni dari
                                    kolom NO (index 0): kalau isinya angka biasa
                                    (1, 2, 3, ...) -> baris data, NO diisi apa adanya,
                                    MATERIAL MDU & kolom REKAP TOTAL diisi apa adanya.
                                    Kalau kolom NO BUKAN angka (mis. "I.", "a.", "b.",
                                    "II.") atau kosong tapi MATERIAL MDU terisi -> baris
                                    ini SECTION, bukan data material: kolom NO
                                    dikosongkan, label section (gabungan isi asli
                                    index 0 + index 1, TIDAK ditambah/dikurangi apa pun)
                                    ditaruh SATU-SATUNYA di kolom MATERIAL MDU, dan
                                    seluruh kolom REKAP TOTAL dikosongkan sebagai sel
                                    terpisah (bukan colspan gabungan) supaya grid tetap
                                    lurus. Baris "TOTAL"/"SISA" (kalau ada) tetap
                                    dikenali seperti sheet flat-table lain. TIDAK ada
                                    data yang diubah/dihapus - hanya PENEMPATAN kolom
                                    saat render yang diperbaiki. Sheet lain TIDAK lewat
                                    sini sama sekali (cabang @else di bawah = logic
                                    lama, tidak diubah).
                                --}}
                                @php
                                    $data          = $r['cells'];
                                    $noValF        = $trim($data[0] ?? '');
                                    $materialValF  = $trim($data[1] ?? '');
                                    $rowKindF      = in_array(mb_strtoupper($noValF), ['TOTAL', 'SISA'], true) ? mb_strtolower($noValF) : null;
                                    $isDataRowF    = !$rowKindF && $noValF !== '' && is_numeric($noValF);
                                    $isSectionRowF = !$rowKindF && !$isDataRowF;

                                    $isSectionMajorF = false;
                                    if ($isSectionRowF) {
                                        $romanCandidateF = rtrim($noValF, '.');
                                        $isSectionMajorF = $romanCandidateF !== '' && preg_match('/^[IVXLCDM]+$/', $romanCandidateF) === 1;
                                    }

                                    $rowClassF = $rowKindF
                                        ? 'row-'.$rowKindF
                                        : ($isSectionRowF ? trim('row-section '.($isSectionMajorF ? 'row-section-major' : '')) : '');
                                @endphp
                                <tr data-row="{{ $r['number'] }}" class="{{ $rowClassF }}">
                                    @if($rowKindF)
                                        {{-- Baris ringkasan TOTAL/SISA: label rata kiri di NO+MATERIAL MDU, sisanya tetap per kolom REKAP TOTAL apa adanya. --}}
                                        <td class="text-left" colspan="2">{{ $noValF }}</td>
                                        @for($rc = 2; $rc < $totalCols; $rc++)
                                            <td class="text-left">{{ $trim($data[$rc] ?? '') }}</td>
                                        @endfor
                                    @elseif($isSectionRowF)
                                        @php
                                            $sectionPartsF   = array_filter([$noValF, $materialValF], fn ($v) => $v !== '');
                                            $sectionLabelF   = implode(' ', $sectionPartsF);
                                        @endphp
                                        <td class="text-left"></td>
                                        <td class="text-left">{{ $sectionLabelF }}</td>
                                        @for($rc = 2; $rc < $totalCols; $rc++)
                                            <td class="text-left"></td>
                                        @endfor
                                    @else
                                        {{-- Baris data material sungguhan: NO = nomor, MATERIAL MDU = nama material, sisanya = REN/RESERVASI/ISSUED/BELUM RESERVASI/BELUM GOOD ISUE. --}}
                                        <td class="text-center">{{ $noValF }}</td>
                                        <td class="text-left">{{ $materialValF }}</td>
                                        @for($rc = 2; $rc < $totalCols; $rc++)
                                            <td class="text-left">{{ $trim($data[$rc] ?? '') }}</td>
                                        @endfor
                                    @endif
                                </tr>
                            @else
                                @php
                                    $data = $r['cells'];

                                    $firstCell = $trim($data[0] ?? '');
                                    $rowKind   = in_array(mb_strtoupper($firstCell), ['TOTAL', 'SISA'], true) ? mb_strtolower($firstCell) : null;

                                    // Baris judul section di tengah sheet (mis. "FUNGSI
                                    // PEMASARAN") - hanya 1 sel yang terisi memenuhi
                                    // lebar baris (bukan pola label:value bertingkat).
                                    $isSectionTitle = !$rowKind && count($buildGroups($data)) === 1 && $firstCell !== '';

                                    $rowClass = $rowKind
                                        ? 'row-'.$rowKind
                                        : ($isSectionTitle ? 'row-section' : '');
                                @endphp

                                <tr data-row="{{ $r['number'] }}" class="{{ $rowClass }}">
                                    @if($rowKind || $isSectionTitle)
                                        @foreach($buildGroups($data) as $g)
                                            <td class="text-left" colspan="{{ $g['width'] }}">{{ $g['label'] }}</td>
                                        @endforeach
                                    @else
                                        @for($k = 0; $k < $totalCols; $k++)
                                            @php
                                                $cell     = $trim($data[$k] ?? '');
                                                $linkInfo = $detectLink($cell);

                                                $navLink = null;
                                                if (!$linkInfo && $cell !== '') {
                                                    if ($canLinkVendorF && $k === $vendorColF) {
                                                        $targetResSheetId = $resVendorMap[\App\Http\Controllers\DashboardController::normalizeVendorKey($cell)] ?? null;
                                                        if ($targetResSheetId && $targetResSheetId != $sheet->id) {
                                                            $navLink = route('sheet.show', $targetResSheetId) . '?vendor=' . urlencode($cell);
                                                        } elseif ($detailKr2026SheetId && (int) $detailKr2026SheetId !== (int) $sheet->id) {
                                                            // PERBAIKAN: Vendor tidak punya sheet RES sendiri -> tetap
                                                            // clickable, fallback ke DETAIL KR 2026 terfilter vendor ybs
                                                            // (lihat $vendorFallbackWarning untuk peringatannya).
                                                            $navLink = route('sheet.show', $detailKr2026SheetId) . '?vendor=' . urlencode($cell);
                                                        }
                                                    } elseif ($canLinkPelangganF && $k === $pelangganColF) {
                                                        $navLink = route('sheet.show', $detailKr2026SheetId) . '?pelanggan=' . urlencode($cell);
                                                    } elseif ($canLinkUraianKrF && $k === $uraianKrColF) {
                                                        $paketValue = $trim($data[$paketColF] ?? '');
                                                        if ($paketValue !== '') {
                                                            $navLink = route('sheet.show', $detailKr2026SheetId) . '?kr=' . urlencode($paketValue);
                                                        }
                                                    }
                                                }

                                                $isNumeric = $cell !== '' && is_numeric(str_replace(',', '', $cell));

                                                // PERBAIKAN: alignment isi tabel dibuat KONSISTEN
                                                // di seluruh sheet - SEMUA cell (teks, angka,
                                                // maupun link) rata kiri, tidak ada lagi
                                                // pengecualian rata tengah/kanan per jenis isi.
                                                // vertical-align:middle sudah diatur secara
                                                // global lewat CSS "tbody td".
                                                $align = 'text-left';
                                            @endphp
                                            <td class="{{ $align }}">
                                                @if($linkInfo)
                                                    <a href="{{ $cell }}" target="_blank" rel="noopener noreferrer" class="cell-link-btn">{{ $linkInfo['icon'] }} {{ $linkInfo['label'] }}</a>
                                                @elseif($navLink)
                                                    <a href="{{ $navLink }}" class="cell-nav-link">{{ $cell }}</a>
                                                @else
                                                    {{ $cell }}
                                                @endif
                                            </td>
                                        @endfor
                                    @endif
                                </tr>
                            @endif
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    @else
    @php
        /*
        |--------------------------------------------------------------------
        | TAMBAHAN (Perbaikan Layout Blok): Sheet Monitoring Tiang (RPB/TA/
        | MAXIMA/WIKA PER SPB, TOTAL VENDOR, REKAP KR) TIDAK dirender sebagai
        | satu tabel datar berisi seluruh baris. Data dipecah dulu jadi
        | beberapa BLOK mengikuti struktur asli file Excel: setiap blok berisi
        | info (Vendor/Nomor Kontrak Rinci/Tanggal/Volume/Fungsi), header
        | tabel (termasuk header bertingkat seperti KEBUTUHAN -> 9/200, 12/200,
        | JUMLAH), baris data, lalu TOTAL & SISA - kemudian blok berikutnya
        | dimulai lagi dari info Vendor berikutnya, dengan jarak rapi di
        | antaranya. Tidak ada perubahan pada database/route/controller -
        | seluruhnya diproses di view ini dari data $rows yang sudah dikirim.
        |--------------------------------------------------------------------
        */

        $decode = function ($row) {
            $d = json_decode($row->row_data ?? '[]', true);
            return is_array($d) ? $d : [];
        };

        $trim = function ($v) {
            return trim((string) ($v ?? ''));
        };

        // TAMBAHAN: Sheet "KUOTA VENDOR" punya satu kolom kosong tanpa
        // header/data/fungsi di paling kiri sebelum kolom "NO" (artefak
        // dari file sumbernya). Kolom ini dihapus HANYA dari TAMPILAN
        // (bukan dari database/data) supaya tabel langsung dimulai dari
        // kolom "NO", tanpa mengubah data atau urutan kolom lainnya.
        // Dicek dulu di SELURUH baris sheet ini (header maupun data) -
        // kolom pertama baru dihapus dari tampilan kalau memang benar-benar
        // kosong di semua baris; kalau ternyata ada isinya di baris mana
        // pun, tampilan tidak disentuh sama sekali. Sheet lain (KHS, RPB/
        // TA/MAXIMA/WIKA PER SPB, Total Vendor, Detail KR 2026, dst) TIDAK
        // terpengaruh oleh perubahan ini.
        $isKuotaVendorSheet = str_contains(mb_strtoupper($trim($sheet->nama_sheet ?? '')), 'KUOTA VENDOR');

        if ($isKuotaVendorSheet) {
            $firstColAlwaysEmpty = true;
            foreach ($rows as $row) {
                $firstCell = $decode($row)[0] ?? '';
                if ($trim($firstCell) !== '') {
                    $firstColAlwaysEmpty = false;
                    break;
                }
            }

            if ($firstColAlwaysEmpty) {
                $decode = function ($row) {
                    $d = json_decode($row->row_data ?? '[]', true);
                    $d = is_array($d) ? $d : [];
                    array_shift($d);
                    return $d;
                };
            }
        }

        // PERBAIKAN: RPB PER SPB dijadikan MASTER TEMPLATE tampilan untuk
        // seluruh modul Monitoring Tiang - panel informasi (VENDOR / NOMOR
        // KONTRAK RINCI / TANGGAL / VOLUME + BAPP/PK/BAPL/BAST/dst) yang
        // dirapikan sebagai tabel sungguhan ini SEKARANG dipakai juga oleh
        // TA PER SPB, MAXIMA PER SPB, dan WIKA PER SPB - bukan cuma RPB -
        // supaya keempatnya memakai component/class yang SAMA PERSIS
        // (rpb-info-table, table-box, dst), sesuai permintaan "RPB sebagai
        // master template". RPB PER SPB sendiri TIDAK diubah sama sekali -
        // hanya sheet ini yang lolos kondisinya sebelumnya, sekarang
        // TA/MAXIMA/WIKA PER SPB juga lolos kondisi yang PERSIS SAMA.
        // Data/kolom/struktur tabel detail tiap sheet tetap murni mengikuti
        // Excel masing-masing (tidak disentuh oleh flag ini) - flag ini
        // HANYA menentukan gaya render panel info di atas tabel. Sheet lain
        // di luar 4 ini (TOTAL VENDOR, REKAP KR, dst) TETAP memakai
        // tampilan panel info yang lama - tidak ada perubahan untuk sheet
        // itu.
        $monitoringTiangMasterSheets = ['RPB PER SPB', 'TA PER SPB', 'MAXIMA PER SPB', 'WIKA PER SPB'];
        $isRpbPerSpb = in_array(mb_strtoupper($trim($sheet->nama_sheet ?? '')), $monitoringTiangMasterSheets, true);

        // TAMBAHAN: Deteksi URL pada isi sel (link ArcGIS, Google Maps,
        // Google Drive, SharePoint, atau link lain dari Excel) dan
        // tampilkan sebagai tombol, tanpa mengubah isi data aslinya
        // (baik di file Excel maupun di database).
        $detectLink = function ($cell) {
            if (!preg_match('#^https?://#i', $cell)) {
                return null;
            }
            $lower = strtolower($cell);
            if (str_contains($lower, 'arcgis') || str_contains($lower, 'google.com/maps') || str_contains($lower, 'maps.google')) {
                return ['icon' => '📍', 'label' => 'Buka Peta'];
            }
            if (str_contains($lower, 'drive.google.com') || str_contains($lower, 'docs.google.com') || str_contains($lower, 'sharepoint.com')) {
                return ['icon' => '📄', 'label' => 'Lihat Dokumen'];
            }
            return ['icon' => '🔗', 'label' => 'Buka Link'];
        };

        // Jumlah kolom sheet = kolom terbanyak di antara seluruh baris (bukan
        // cuma beberapa baris pertama), karena blok yang berbeda di sheet yang
        // sama bisa punya jumlah kolom terisi yang berbeda-beda.
        $totalCols = 1;
        foreach ($rows as $row) {
            $totalCols = max($totalCols, count($decode($row)));
        }

        $isBlankCells = function ($cells) use ($trim) {
            foreach ($cells as $v) {
                if ($trim($v) !== '') {
                    return false;
                }
            }
            return true;
        };

        // Baris dianggap baris HEADER TABEL kalau mayoritas sel yang terisi
        // berupa teks (bukan angka) dan cukup banyak sel yang terisi - sama
        // seperti heuristik yang sudah dipakai sebelumnya, supaya baris berisi
        // angka urut (mis. hasil formula =B1+1) tidak salah terdeteksi sebagai
        // header.
        $isHeaderCandidate = function ($cells) use ($totalCols, $trim) {
            $filled = 0;
            $textCount = 0;
            foreach ($cells as $v) {
                $t = $trim($v);
                if ($t !== '') {
                    $filled++;
                    if (!is_numeric($t)) {
                        $textCount++;
                    }
                }
            }
            $ratio     = $totalCols > 0 ? ($filled / $totalCols) : 0;
            $textRatio = $filled > 0 ? ($textCount / $filled) : 0;

            return $filled >= 3 && $ratio >= 0.4 && $textRatio >= 0.5;
        };

        // Kelompokkan kolom yang di Excel-nya merged cell (sekarang jadi sel
        // kosong berturut-turut) supaya tetap terlihat menyatu (colspan),
        // TANPA menggabung isi teksnya.
        $buildGroups = function ($cells) use ($totalCols, $trim) {
            $groups = [];
            $c = 0;
            while ($c < $totalCols) {
                $label = $trim($cells[$c] ?? '');
                $width = 1;
                $j = $c + 1;
                while ($j < $totalCols && $trim($cells[$j] ?? '') === '') {
                    $width++;
                    $j++;
                }
                $groups[] = ['start' => $c, 'width' => $width, 'label' => $label];
                $c += $width;
            }
            return $groups;
        };

        // TAMBAHAN: parser khusus panel info sheet "RPB per SPB" - mengurai
        // baris-baris info (sebelum header tabel) jadi pasangan label:nilai
        // yang rapi untuk kolom kiri (VENDOR / NOMOR KONTRAK RINCI /
        // TANGGAL / VOLUME) dan kolom kanan (BAPP / PK / BAPL atau info
        // lain persis posisinya di spreadsheet asli), plus baris VOLUME
        // diurai jadi daftar item (nama, jumlah, satuan) + baris total.
        // TIDAK mengubah isi data sama sekali - cuma dikelompokkan ulang
        // untuk keperluan tampilan (dipakai HANYA saat $isRpbPerSpb true).
        $parseRpbInfoPanel = function ($infoLines) use ($buildGroups, $trim) {
            $left        = [];
            $right       = [];
            $volumeItems = [];
            $volumeTotal = null;
            $inVolume    = false;

            foreach ($infoLines as $r) {
                $groups = $buildGroups($r['cells']);
                if (empty($groups)) { continue; }

                $nonEmpty = array_values(array_filter($groups, fn ($g) => $trim($g['label']) !== ''));
                if (empty($nonEmpty)) { continue; }

                if ($inVolume) {
                    // Baris total volume: cuma 1 sel terisi & isinya angka
                    // murni (mis. "146"), sejajar kolom jumlah - bukan item.
                    if (count($nonEmpty) === 1) {
                        $only   = $trim($nonEmpty[0]['label']);
                        $number = preg_replace('/[.,\s]/', '', $only);
                        if ($number !== '' && is_numeric($number)) {
                            $volumeTotal = $only;
                            continue;
                        }
                        // Bukan angka -> bukan bagian VOLUME lagi.
                        $inVolume = false;
                        $left[]   = ['label' => $only, 'value' => '', 'continuation' => false];
                        continue;
                    }

                    $name = $trim($nonEmpty[0]['label']);
                    $qty  = '';
                    $unit = '';
                    if (isset($nonEmpty[1])) {
                        $second = $trim($nonEmpty[1]['label']);
                        if (preg_match('/^([\d.,]+)\s*(\S*)$/', $second, $m)) {
                            $qty  = $m[1];
                            $unit = $m[2];
                        } else {
                            $qty = $second;
                        }
                    }
                    if ($unit === '' && isset($nonEmpty[2])) {
                        $unit = $trim($nonEmpty[2]['label']);
                    }
                    $volumeItems[] = ['name' => $name, 'qty' => $qty, 'unit' => $unit];
                    continue;
                }

                $label = $trim($groups[0]['label']);

                if (mb_strtoupper($label) === 'VOLUME') {
                    $inVolume = true;
                    $left[]   = ['label' => 'VOLUME', 'value' => '', 'continuation' => false];
                    continue;
                }

                // Cari nilai kiri: grup non-kosong pertama setelah label.
                $rest     = array_slice($groups, 1);
                $valueIdx = null;
                $value    = '';
                foreach ($rest as $idx => $g) {
                    if ($trim($g['label']) !== '') {
                        $valueIdx = $idx;
                        $value    = $trim($g['label']);
                        break;
                    }
                }

                if ($label !== '') {
                    $left[] = ['label' => $label, 'value' => $value, 'continuation' => false];
                } elseif ($value !== '' && !empty($left)) {
                    // Baris tanpa label kiri tapi ada nilai -> sambungan
                    // dari baris kiri sebelumnya (mis. tanggal 2 baris).
                    $lastIdx = count($left) - 1;
                    $left[$lastIdx]['value'] = trim($left[$lastIdx]['value'] . ' ' . $value);
                }

                // Sisa grup setelah nilai kiri = info tambahan di kanan
                // (BAPP/PK/BAPL atau info lain), sesuai posisi aslinya.
                $after = $valueIdx !== null ? array_slice($rest, $valueIdx + 1) : $rest;
                $after = array_values(array_filter($after, fn ($g) => $trim($g['label']) !== ''));

                if (!empty($after)) {
                    if (count($after) >= 2) {
                        $rLabel = $trim($after[0]['label']);
                        $rValue = $trim($after[1]['label']);
                        if (count($after) > 2) {
                            $extra  = array_slice($after, 2);
                            $rValue = trim($rValue . ' ' . implode(' ', array_map(fn ($g) => $trim($g['label']), $extra)));
                        }
                        $right[] = ['label' => $rLabel, 'value' => $rValue];
                    } else {
                        $only = $trim($after[0]['label']);
                        if (!empty($right)) {
                            $lastR                  = count($right) - 1;
                            $right[$lastR]['value'] = trim($right[$lastR]['value']) . "\n" . $only;
                        } else {
                            $right[] = ['label' => '', 'value' => $only];
                        }
                    }
                }
            }

            return [
                'left'        => $left,
                'right'       => $right,
                'volumeItems' => $volumeItems,
                'volumeTotal' => $volumeTotal,
            ];
        };

        $allRows = [];
        foreach ($rows as $row) {
            $allRows[] = ['number' => $row->row_number, 'cells' => $decode($row)];
        }

        // TAMBAHAN: Titik mulai tiap blok = baris yang sel pertamanya persis
        // "VENDOR" (dipakai sheet RPB/TA/MAXIMA/WIKA PER SPB, satu blok per
        // Vendor/Kontrak). Kalau sheet ini sama sekali tidak punya baris
        // "VENDOR" (mis. TOTAL VENDOR, REKAP KR yang strukturnya beda -
        // per Tahun, bukan per Vendor), blok dipisah berdasarkan baris kosong
        // sebagai pemisah section, sesuai bentuk aslinya di Excel.
        $vendorAnchors = [];
        foreach ($allRows as $i => $r) {
            if (mb_strtoupper($trim($r['cells'][0] ?? '')) === 'VENDOR') {
                $vendorAnchors[] = $i;
            }
        }

        $rawBlocks = [];
        if (!empty($vendorAnchors)) {
            if ($vendorAnchors[0] > 0) {
                $pre = array_slice($allRows, 0, $vendorAnchors[0]);
                $hasContent = false;
                foreach ($pre as $r) {
                    if (!$isBlankCells($r['cells'])) { $hasContent = true; break; }
                }
                if ($hasContent) {
                    $rawBlocks[] = $pre;
                }
            }
            foreach ($vendorAnchors as $idx => $start) {
                $end = $vendorAnchors[$idx + 1] ?? count($allRows);
                $rawBlocks[] = array_slice($allRows, $start, $end - $start);
            }
        } else {
            $current = [];
            foreach ($allRows as $r) {
                if ($isBlankCells($r['cells'])) {
                    if (!empty($current)) { $rawBlocks[] = $current; $current = []; }
                    continue;
                }
                $current[] = $r;
            }
            if (!empty($current)) {
                $rawBlocks[] = $current;
            }
        }

        // Pecah tiap blok jadi: baris info (sebelum header tabel), baris
        // header (+ sub-header kalau kolomnya bertingkat mis. KEBUTUHAN ->
        // 9/200, 12/200, JUMLAH), dan baris data (termasuk TOTAL & SISA di
        // dalamnya, persis posisi aslinya).
        $parseBlock = function ($blockRows) use ($isHeaderCandidate, $buildGroups, $trim) {
            $headerIdx = null;
            // PERBAIKAN: sebelumnya cuma scan 15 baris pertama, sehingga
            // blok dengan baris VOLUME panjang (banyak jenis tiang, mis.
            // sheet "RPB per SPB") membuat baris header tabel sungguhan
            // (NO/LOKASI PEKERJAAN/...) tidak pernah ketemu dan seluruh
            // blok (termasuk data) salah dianggap baris info. Batas
            // dinaikkan supaya tetap aman untuk semua sheet lain (kalau
            // header sudah ketemu di 15 baris pertama, hasilnya tetap sama
            // persis seperti sebelumnya).
            $scanLimit = min(80, count($blockRows));

            // PERBAIKAN UTAMA: baris header tabel detail Monitoring Tiang
            // (RPB/TA/MAXIMA/WIKA PER SPB) SELALU diawali sel "NO" persis
            // seperti di Excel-nya - jadi ini dicek DULU sebagai penanda
            // paling akurat, sebelum heuristik rasio sel-terisi lama.
            // Heuristik lama bisa gagal (false negative) untuk blok yang
            // kolom terpakainya lebih sedikit dari kolom terbanyak di
            // seluruh sheet (mis. blok tanpa kolom REKON/NB di ujung kanan),
            // sehingga rasio sel-terisi jatuh di bawah ambang batas dan
            // baris header sungguhan tidak pernah ketemu - akibatnya SEMUA
            // baris di blok itu (termasuk NO/LOKASI PEKERJAAN/UNIT/dst)
            // salah dianggap baris info dan berantakan saat ditampilkan.
            // Kalau sel "NO" tidak ketemu sama sekali di blok ini (sheet
            // lain yang headernya tidak diawali "NO"), tetap fallback ke
            // heuristik rasio lama seperti sebelumnya - tidak ada yang
            // berubah untuk sheet-sheet itu.
            for ($i = 0; $i < $scanLimit; $i++) {
                if (mb_strtoupper($trim($blockRows[$i]['cells'][0] ?? '')) === 'NO') {
                    $headerIdx = $i;
                    break;
                }
            }

            if ($headerIdx === null) {
                for ($i = 0; $i < $scanLimit; $i++) {
                    if ($isHeaderCandidate($blockRows[$i]['cells'])) {
                        $headerIdx = $i;
                        break;
                    }
                }
            }

            $infoRows     = $headerIdx !== null ? array_slice($blockRows, 0, $headerIdx) : $blockRows;
            $headerRow    = $headerIdx !== null ? $blockRows[$headerIdx]['cells'] : null;
            $subHeaderRow = null;
            $dataStart    = $headerIdx !== null ? $headerIdx + 1 : count($blockRows);

            if ($headerRow !== null) {
                $groups      = $buildGroups($headerRow);
                $groupedCols = array_filter($groups, fn ($g) => $g['width'] > 1);

                if (!empty($groupedCols) && isset($blockRows[$headerIdx + 1])) {
                    $next = $blockRows[$headerIdx + 1]['cells'];
                    $ok   = true;

                    foreach ($groups as $g) {
                        $slice  = array_slice($next, $g['start'], $g['width']);
                        $filled = false;
                        foreach ($slice as $v) {
                            if ($trim($v) !== '') { $filled = true; break; }
                        }
                        if ($g['width'] === 1 && $filled) { $ok = false; break; }
                        if ($g['width'] > 1 && !$filled)  { $ok = false; break; }
                    }

                    if ($ok) {
                        $subHeaderRow = $next;
                        $dataStart    = $headerIdx + 2;
                    }
                }
            }

            $dataRows = $headerRow !== null ? array_slice($blockRows, $dataStart) : [];

            return [
                'infoRows'     => $infoRows,
                'headerRow'    => $headerRow,
                'subHeaderRow' => $subHeaderRow,
                'dataRows'     => $dataRows,
            ];
        };

        $blocks = array_map($parseBlock, $rawBlocks);

        // TAMBAHAN: Deteksi kolom "Vendor"/"Penyedia", "Nama Pelanggan", dan
        // "Uraian Pekerjaan (KR)"/PAKET/Kontrak Rinci dari header SEMUA blok
        // (bukan cuma blok pertama seperti sebelumnya), supaya fitur klik
        // Penyedia -> RES miliknya dan klik Nama Pelanggan / Uraian Pekerjaan
        // (KR) -> Detail KR 2026 tetap berjalan sama seperti sebelumnya di
        // seluruh sheet yang punya kolom tersebut.
        $vendorCol       = null;
        $pelangganCol    = null;
        $uraianKrCol     = null;
        $paketCol        = null;
        $kontrakRinciCol = null;
        $lokasiCol       = null;

        foreach ($blocks as $b) {
            foreach ([$b['headerRow'], $b['subHeaderRow']] as $hrow) {
                if (!$hrow) continue;
                foreach ($hrow as $colIdx => $headerVal) {
                    $headerVal = mb_strtolower($trim($headerVal));
                    if ($headerVal === '') continue;
                    if ($vendorCol === null && (str_contains($headerVal, 'vendor') || str_contains($headerVal, 'penyedia'))) {
                        $vendorCol = $colIdx;
                    }
                    if ($pelangganCol === null && str_contains($headerVal, 'pelanggan')) {
                        $pelangganCol = $colIdx;
                    }
                    if ($uraianKrCol === null && str_contains($headerVal, 'uraian pekerjaan')) {
                        $uraianKrCol = $colIdx;
                    }
                    if ($kontrakRinciCol === null && str_contains($headerVal, 'kontrak')) {
                        $kontrakRinciCol = $colIdx;
                    }
                    if ($paketCol === null && str_contains($headerVal, 'paket')) {
                        $paketCol = $colIdx;
                    }
                    if ($lokasiCol === null && str_contains($headerVal, 'lokasi')) {
                        $lokasiCol = $colIdx;
                    }
                }
            }
        }

        // PERBAIKAN: Vendor/Penyedia yang TIDAK memiliki sheet RES sendiri
        // tetap harus clickable (fallback ke DETAIL KR 2026 dengan filter
        // vendor - lihat blok $navLink di bawah), bukan hanya vendor yang
        // sudah ada di $resVendorMap.
        $canLinkVendor    = $vendorCol !== null && (!empty($resVendorMap) || ($detailKr2026SheetId && (int) $detailKr2026SheetId !== (int) $sheet->id));
        $canLinkPelanggan = $pelangganCol !== null && $detailKr2026SheetId && $detailKr2026SheetId != $sheet->id;
        $canLinkUraianKr  = $uraianKrCol !== null && $paketCol !== null
            && $detailKr2026SheetId && $detailKr2026SheetId != $sheet->id;

        $filterCol = null;
        if ($filterValue !== '') {
            $filterCol = $filterLabel === 'Penyedia'
                ? $vendorCol
                : ($filterLabel === 'Nama Pelanggan' ? $pelangganCol : (in_array($filterLabel, ['Uraian Pekerjaan (KR)', 'Paket / Kontrak Rinci'], true) ? ($kontrakRinciCol ?? $paketCol) : ($filterLabel === 'Lokasi Pekerjaan / Detail Pekerjaan' ? $lokasiCol : null)));
        }

        // TAMBAHAN: Lebar kolom proporsional mendekati Excel (kolom NO kecil,
        // LOKASI PEKERJAAN/KETERANGAN lebar, dst) berdasarkan nama kolom pada
        // header/sub-header tiap blok - bukan semua kolom disamakan lebarnya.
        $widthFor = function ($label) {
            $l = mb_strtoupper(trim($label));
            if ($l === '') return 110;
            if ($l === 'NO') return 64;
            if (str_contains($l, 'LOKASI')) return 340;
            if (str_contains($l, 'KETERANGAN') || str_contains($l, 'KENDALA')) return 340;
            if (str_contains($l, 'VENDOR')) return 230;
            if ($l === 'UNIT') return 190;
            if (str_contains($l, 'TGL') || str_contains($l, 'TANGGAL')) return 150;
            if (str_contains($l, 'WO')) return 180;
            if (str_contains($l, 'PRIORITAS')) return 130;
            if (str_contains($l, 'FUNGSI')) return 150;
            if (str_contains($l, 'PROGRES')) return 170;
            if (str_contains($l, 'GAMBAR')) return 120;
            if (str_contains($l, 'REKON')) return 140;
            if (preg_match('/\d\s*[\/\-]\s*\d/', $l)) return 120;
            if (str_contains($l, 'JUMLAH') || $l === 'TOTAL') return 120;
            if (str_contains($l, 'KR')) return 160;
            return 150;
        };

        $hasAnyContent = false;
        foreach ($blocks as $b) {
            if ($b['headerRow'] !== null) { $hasAnyContent = true; break; }
            foreach ($b['infoRows'] as $r) {
                if (!$isBlankCells($r['cells'])) { $hasAnyContent = true; break 2; }
            }
        }
    @endphp

    @if(!$hasAnyContent)
        <div class="table-box">
            <div class="empty-state">Tidak ada data untuk ditampilkan.</div>
        </div>
    @endif

    @foreach($blocks as $block)
        @php
            $infoLines = [];
            foreach ($block['infoRows'] as $r) {
                if (!$isBlankCells($r['cells'])) { $infoLines[] = $r; }
            }
            $onlyTitleLine = count($infoLines) === 1 && $block['headerRow'] === null;

            // TAMBAHAN: baris judul section seperti "FUNGSI PEMASARAN" / "FUNGSI
            // DISTRIBUSI" (hanya 1 sel terisi, memenuhi lebar baris - tanpa
            // label:value seperti VENDOR/KONTRAK/TANGGAL/VOLUME) dipisah dari
            // panel info dan ditampilkan sebagai judul biasa tepat di atas
            // tabelnya, persis posisinya di Excel - bukan ikut masuk ke kotak
            // info VENDOR/KONTRAK/TANGGAL/VOLUME.
            $sectionTitleLines = [];
            if (!$onlyTitleLine && $block['headerRow'] !== null) {
                while (!empty($infoLines)) {
                    $last = end($infoLines);
                    if (count($buildGroups($last['cells'])) === 1) {
                        array_unshift($sectionTitleLines, $last);
                        array_pop($infoLines);
                    } else {
                        break;
                    }
                }
            }

            // TAMBAHAN: khusus sheet "RPB per SPB", urai $infoLines jadi
            // struktur panel rapi (label:nilai kiri, BAPP/PK/BAPL kanan,
            // daftar VOLUME) - null kalau bukan sheet ini, sehingga sheet
            // lain tetap pakai tampilan lama persis seperti sebelumnya.
            $rpbPanel = ($isRpbPerSpb && !$onlyTitleLine && !empty($infoLines))
                ? $parseRpbInfoPanel($infoLines)
                : null;
        @endphp

        @if($onlyTitleLine || !empty($infoLines) || !empty($sectionTitleLines) || $block['headerRow'] !== null)
            <div class="sheet-block">
                @if($onlyTitleLine)
                    <div class="sheet-block-title">{{ $trim($infoLines[0]['cells'][0] ?? '') }}</div>
                @elseif(!empty($infoLines))
                    @if($rpbPanel)
                        {{-- PERBAIKAN: panel info sheet "RPB per SPB" sekarang
                             berbentuk TABLE sungguhan (thead/tbody), memakai
                             class tabel yang sama dengan tabel detail sheet
                             Monitoring Tiang lain (header biru/putih/bold,
                             border, dst) - BUKAN lagi layout flex/grid dua
                             kolom. VOLUME memakai rowspan supaya tiap jenis
                             tiang + TOTAL tetap satu baris tabel sendiri.
                             Info tambahan (SPK NO/BAPP/TANGGAL SPK/PK/BAPL,
                             kalau ada di sumbernya) ditambahkan sebagai baris
                             tabel juga, bukan blok teks terpisah. --}}
                        <div class="table-box rpb-info-table-box">
                            <table class="rpb-info-table">
                                <thead>
                                    <tr>
                                        <th class="rpb-info-th-label">INFORMASI</th>
                                        <th>NILAI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rpbPanel['left'] as $row)
                                        @if($row['label'] === 'VOLUME')
                                            @php
                                                $volRowCount = count($rpbPanel['volumeItems']) + ($rpbPanel['volumeTotal'] !== null ? 1 : 0);
                                                $volRowCount = max($volRowCount, 1);
                                                $volLabelPrinted = false;
                                            @endphp
                                            @forelse($rpbPanel['volumeItems'] as $item)
                                                <tr>
                                                    @if(!$volLabelPrinted)
                                                        <td rowspan="{{ $volRowCount }}" class="text-left">VOLUME</td>
                                                        @php $volLabelPrinted = true; @endphp
                                                    @endif
                                                    <td class="text-left">{{ trim($item['name'] . ' - ' . trim($item['qty'] . ' ' . $item['unit'])) }}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                            @if($rpbPanel['volumeTotal'] !== null)
                                                <tr class="row-total">
                                                    @if(!$volLabelPrinted)
                                                        <td rowspan="{{ $volRowCount }}" class="text-left">VOLUME</td>
                                                        @php $volLabelPrinted = true; @endphp
                                                    @endif
                                                    <td class="text-left">TOTAL - {{ $rpbPanel['volumeTotal'] }}</td>
                                                </tr>
                                            @elseif(!$volLabelPrinted)
                                                <tr>
                                                    <td class="text-left">VOLUME</td>
                                                    <td class="text-left">{{ $row['value'] }}</td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td class="text-left">{{ $row['label'] }}</td>
                                                <td class="text-left rpb-value-multiline">{{ $row['value'] }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @foreach($rpbPanel['right'] as $row)
                                        <tr>
                                            <td class="text-left">{{ $row['label'] }}</td>
                                            <td class="text-left rpb-value-multiline">{{ $row['value'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="sheet-info">
                            @foreach($infoLines as $r)
                                <div class="sheet-info-row">
                                    @foreach($buildGroups($r['cells']) as $g)
                                        <div class="sheet-info-cell">{{ $g['label'] }}</div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                @foreach($sectionTitleLines as $r)
                    <div class="sheet-section-title">{{ $trim($r['cells'][0] ?? '') }}</div>
                @endforeach

                @if($block['headerRow'])
                    @php
                        // Label per kolom (dipakai untuk lebar kolom proporsional):
                        // pakai label sub-header kalau ada, kalau tidak pakai label
                        // header utama, kalau kosong juga (kolom di bawah header
                        // yang di-merge) warisi label header utama terakhir.
                        $colLabel = array_fill(0, $totalCols, '');
                        $lastMain = '';
                        for ($c = 0; $c < $totalCols; $c++) {
                            $sub  = $block['subHeaderRow'] ? $trim($block['subHeaderRow'][$c] ?? '') : '';
                            $main = $trim($block['headerRow'][$c] ?? '');
                            if ($main !== '') { $lastMain = $main; }
                            $colLabel[$c] = $sub !== '' ? $sub : ($main !== '' ? $main : $lastMain);
                        }

                        $mainGroups = $buildGroups($block['headerRow']);

                        // Terapkan filter Penyedia/Nama Pelanggan/Uraian Pekerjaan (KR)
                        // (kalau ada dan kolomnya ada di blok ini) - sama seperti
                        // sebelumnya, hanya sekarang per-blok.
                        $dataRows = $block['dataRows'];
                        if ($filterCol !== null) {
                            $dataRows = array_values(array_filter($dataRows, function ($r) use ($filterCol, $trim, $filterValue) {
                                return mb_strtolower($trim($r['cells'][$filterCol] ?? '')) === mb_strtolower($filterValue);
                            }));
                        }
                    @endphp

                    <div class="table-box">
                        <table>
                            <colgroup>
                                @for($c = 0; $c < $totalCols; $c++)
                                    <col style="width:{{ $widthFor($colLabel[$c]) }}px;">
                                @endfor
                            </colgroup>
                            <thead>
                                <tr>
                                    @foreach($mainGroups as $g)
                                        <th style="top:0px;" colspan="{{ $g['width'] }}" @if($g['width'] === 1 && $block['subHeaderRow']) rowspan="2" @endif>
                                            {!! $g['label'] !== '' ? e($g['label']) : '&nbsp;' !!}
                                        </th>
                                    @endforeach
                                </tr>
                                @if($block['subHeaderRow'])
                                    <tr>
                                        @foreach($mainGroups as $g)
                                            @if($g['width'] > 1)
                                                @php
                                                    $subGroups = [];
                                                    $sc = $g['start'];
                                                    $send = $g['start'] + $g['width'];
                                                    while ($sc < $send) {
                                                        $slabel = $trim($block['subHeaderRow'][$sc] ?? '');
                                                        $sw = 1;
                                                        $sj = $sc + 1;
                                                        while ($sj < $send && $trim($block['subHeaderRow'][$sj] ?? '') === '') {
                                                            $sw++; $sj++;
                                                        }
                                                        $subGroups[] = ['width' => $sw, 'label' => $slabel];
                                                        $sc += $sw;
                                                    }
                                                @endphp
                                                @foreach($subGroups as $sg)
                                                    <th style="top:38px;" colspan="{{ $sg['width'] }}">{!! $sg['label'] !== '' ? e($sg['label']) : '&nbsp;' !!}</th>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tr>
                                @endif
                            </thead>

                            <tbody>
                                @forelse($dataRows as $r)
                                    @php
                                        $data = $r['cells'];
                                        if ($isBlankCells($data)) continue;

                                        $firstCell = $trim($data[0] ?? '');
                                        $rowKind   = in_array(mb_strtoupper($firstCell), ['TOTAL', 'SISA'], true) ? mb_strtolower($firstCell) : null;

                                        // TAMBAHAN: baris judul sub-section DI TENGAH data (mis.
                                        // "Belum masuk WO" / "Tidak jadi di pasang" pada sheet WIKA
                                        // PER SPB) - hanya sel pertama (kolom NO) yang terisi, sisanya
                                        // kosong, dan isinya bukan angka/TOTAL/SISA. Baris ini TETAP di
                                        // tabel yang sama (kolomnya identik dengan tabel di atasnya,
                                        // persis strukturnya di Excel) tapi ditampilkan sebagai baris
                                        // judul terpisah (colspan penuh, gaya sama seperti judul
                                        // "FUNGSI PEMASARAN"/"FUNGSI DISTRIBUSI") - bukan ikut jadi
                                        // baris data biasa yang datanya jadi berantakan di kolom NO.
                                        if ($rowKind === null && $firstCell !== '' && !is_numeric(str_replace(',', '', $firstCell))) {
                                            $restFilled = false;
                                            for ($ci = 1; $ci < $totalCols; $ci++) {
                                                if ($trim($data[$ci] ?? '') !== '') { $restFilled = true; break; }
                                            }
                                            if (!$restFilled) {
                                                $rowKind = 'section';
                                            }
                                        }
                                    @endphp

                                    <tr data-row="{{ $r['number'] }}" class="{{ $rowKind ? 'row-'.$rowKind : '' }}">
                                        @if($rowKind)
                                            @foreach($buildGroups($data) as $g)
                                                <td class="text-left" colspan="{{ $g['width'] }}">{{ $g['label'] }}</td>
                                            @endforeach
                                        @else
                                            @for($k = 0; $k < $totalCols; $k++)
                                                @php
                                                    $cell     = $trim($data[$k] ?? '');
                                                    $linkInfo = $detectLink($cell);

                                                    $navLink = null;
                                                    if (!$linkInfo && $cell !== '') {
                                                        if ($canLinkVendor && $k === $vendorCol) {
                                                            $targetResSheetId = $resVendorMap[\App\Http\Controllers\DashboardController::normalizeVendorKey($cell)] ?? null;
                                                            if ($targetResSheetId && $targetResSheetId != $sheet->id) {
                                                                $navLink = route('sheet.show', $targetResSheetId) . '?vendor=' . urlencode($cell);
                                                            } elseif ($detailKr2026SheetId && (int) $detailKr2026SheetId !== (int) $sheet->id) {
                                                                // PERBAIKAN: Vendor tidak punya sheet RES sendiri -> tetap
                                                                // clickable, fallback ke DETAIL KR 2026 terfilter vendor ybs
                                                                // (lihat $vendorFallbackWarning untuk peringatannya).
                                                                $navLink = route('sheet.show', $detailKr2026SheetId) . '?vendor=' . urlencode($cell);
                                                            }
                                                        } elseif ($canLinkPelanggan && $k === $pelangganCol) {
                                                            $navLink = route('sheet.show', $detailKr2026SheetId) . '?pelanggan=' . urlencode($cell);
                                                        } elseif ($canLinkUraianKr && $k === $uraianKrCol) {
                                                            $paketValue = $trim($data[$paketCol] ?? '');
                                                            if ($paketValue !== '') {
                                                                $navLink = route('sheet.show', $detailKr2026SheetId) . '?kr=' . urlencode($paketValue);
                                                            }
                                                        }
                                                    }

                                                    $isNumeric = $cell !== '' && is_numeric(str_replace(',', '', $cell));

                                                    // PERBAIKAN: alignment isi tabel dibuat
                                                    // KONSISTEN - SEMUA cell rata kiri, tidak
                                                    // ada lagi pengecualian rata tengah untuk
                                                    // teks pendek (<=6 karakter) atau rata
                                                    // kanan untuk angka. vertical-align:middle
                                                    // sudah diatur secara global lewat CSS
                                                    // "tbody td".
                                                    $align = 'text-left';
                                                @endphp
                                                <td class="{{ $align }}">
                                                    @if($linkInfo)
                                                        <a href="{{ $cell }}" target="_blank" rel="noopener noreferrer" class="cell-link-btn">{{ $linkInfo['icon'] }} {{ $linkInfo['label'] }}</a>
                                                    @elseif($navLink)
                                                        <a href="{{ $navLink }}" class="cell-nav-link">{{ $cell }}</a>
                                                    @else
                                                        {{ $cell }}
                                                    @endif
                                                </td>
                                            @endfor
                                        @endif
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    @endforeach
    @endif

</div>

<script>
// ================= PERBAIKAN: Tombol Back mengikuti riwayat navigasi =================
// Kalau ada riwayat halaman sebelumnya di sesi browser ini (mis. datang dari
// Dashboard -> KHS JASA 2026 -> RES MFS), tombol Back akan kembali satu
// langkah lewat browser history (window.history.back()), bukan selalu ke
// Dashboard. Kalau halaman ini dibuka langsung (refresh / link luar / tab
// baru), tidak ada riwayat untuk kembali, sehingga href asli (Dashboard)
// tetap dipakai sebagai fallback.
(function(){
    const btnBack = document.getElementById('btnBackHistory');
    if (!btnBack) return;

    btnBack.addEventListener('click', function(e){
        if (window.history.length > 1 && document.referrer) {
            e.preventDefault();
            window.history.back();
        }
    });
})();

// ================= TAMBAHAN: Highlight & Auto-scroll hasil pencarian =================
(function(){
    const params = new URLSearchParams(window.location.search);
    const highlightRow = params.get('highlight_row');

    if (!highlightRow) return;

    const target = document.querySelector('tr[data-row="' + highlightRow + '"]');
    if (!target) return;

    setTimeout(function(){
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        target.classList.add('row-highlight');
        setTimeout(function(){ target.classList.remove('row-highlight'); }, 4000);
    }, 200);
})();
</script>

</body>
</html>