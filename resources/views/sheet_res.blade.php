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

        /* TAMBAHAN: Bungkus tombol Kembali & Dashboard supaya rapi
           berdampingan, sama seperti pola header-actions di halaman
           GANTER/sheet lainnya. */
        .header-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }

        /* ================= INFO BAR (PELAKSANA / KONTRAK / JUDUL) ================= */

        .info-box{
            background:#1565C0;
            color:#fff;
            border-radius:10px 10px 0 0;
            padding:10px 16px;
            font-size:13px;
        }

        .info-row{
            display:flex;
            align-items:center;
            padding:3px 0;
        }

        .info-row + .info-row{
            border-top:1px solid rgba(255,255,255,.25);
        }

        .info-label{
            width:150px;
            min-width:150px;
            font-weight:bold;
        }

        .info-value{
            font-weight:normal;
            word-break:break-word;
        }

        .info-title{
            font-size:16px;
            font-weight:bold;
            text-align:center;
            padding:4px 0;
        }

        /* ================= TABLE WRAPPER ================= */

        .table-box{
            background:#fff;
            border-radius:0 0 12px 12px;
            overflow:auto;
            max-height:78vh;
            position:relative;
            box-shadow:0 4px 15px rgba(0,0,0,.12);
            border:1px solid #000;
            border-top:none;
        }

        table{
            border-collapse:separate;
            border-spacing:0;
            table-layout:fixed;
            width:max-content;
            min-width:100%;
        }

        thead th{
            border-top:1px solid #000;
            border-left:1px solid #000;
            border-bottom:1px solid #000;
            padding:5px 6px;
            text-align:center;
            font-size:12px;
            font-weight:bold;
            vertical-align:middle;
            position:sticky;
            z-index:30;
        }

        thead th:last-child{
            border-right:1px solid #000;
        }

        /* level warna header mengikuti Excel */
        .hdr-fixed{
            background:#DDEBF7;
            color:#000;
        }

        .hdr-paket{
            background:#FFFF00;
            color:#000;
        }

        .hdr-pic{
            background:#B7B7B7;
            color:#000;
        }

        .hdr-sub{
            background:#B7B7B7;
            color:#000;
        }

        tbody td{
            border-top:1px solid #000;
            border-left:1px solid #000;
            border-bottom:1px solid #000;
            padding:3px 6px;
            font-size:12px;
            vertical-align:middle;
            background:#fff;
        }

        tbody td:last-child{
            border-right:1px solid #000;
        }

        tbody tr:nth-child(even) td{
            background:#F7FBFF;
        }

        tbody tr:hover td{
            background:#DCEEFF !important;
        }

        /* baris kategori romawi (mis. I. PEKERJAAN ...) */
        tr.row-category td.cat-cell{
            background:#8EA9DB !important;
            font-weight:bold;
            color:#000;
        }

        /* baris sub kategori (mis. a. MATERIAL UTAMA :) */
        tr.row-subcategory td.cat-cell{
            font-weight:bold;
            background:#fff;
        }

        /* baris footer / status */
        tr.row-footer td{
            background:#FCE4D6 !important;
            font-weight:bold;
        }

        /* ================= TAMBAHAN: HIGHLIGHT HASIL PENCARIAN ================= */

        tr.row-highlight td{
            background:#fff3b0 !important;
            transition:background 1.5s ease;
        }

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

        /* ================= TAMBAHAN: EMPTY STATE & FILTER BANNER ================= */

        .empty-state{
            padding:40px;
            text-align:center;
            color:#888;
            font-size:14px;
        }

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

        .text-left{ text-align:left; }
        .text-center{ text-align:center; }
        .text-right{ text-align:right; }

        /* ================= STICKY (FREEZE) ================= */

        .sticky-col{
            position:sticky;
            z-index:10;
        }

        thead .sticky-col{
            z-index:40;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width:768px){
            .page-header h2{ font-size:20px; }
            .page-header p{ font-size:12px; }
            .btn{ padding:8px 14px; font-size:12px; }
            .table-box{ max-height:72vh; }
            thead th, tbody td{ font-size:11px; padding:3px 4px; }
            .info-label{ width:110px; min-width:110px; }
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

        {{-- PERBAIKAN: Tombol Back sekarang mengikuti riwayat navigasi (browser history)
             satu langkah ke belakang - bukan selalu ke Dashboard. href tetap mengarah
             ke Dashboard sebagai fallback (kalau JS mati, atau halaman ini dibuka
             langsung lewat URL tanpa riwayat sebelumnya). --}}
        <div class="header-actions">
            <a href="{{ route('dashboard') }}" class="btn" id="btnBackHistory">
                &larr; Kembali
            </a>

            {{-- TAMBAHAN: Tombol Dashboard - selalu langsung ke Dashboard,
                 sama persis desain/posisi/ikon dengan tombol Dashboard di
                 halaman GANTER dan sheet lainnya. --}}
            <a href="{{ route('dashboard') }}" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px; margin-right:4px;">
                    <path d="M3 11.5 12 4l9 7.5"></path>
                    <path d="M5.5 10v9a1 1 0 0 0 1 1H9.5a1 1 0 0 0 1-1V15a1 1 0 0 1 1-1H12.5a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1H17.5a1 1 0 0 0 1-1v-9"></path>
                </svg>Dashboard
            </a>
        </div>
    </div>

    @php
        // TAMBAHAN: Filter server-side dari klik Vendor/Nama Pelanggan di Dashboard
        $filterValue = trim((string) request('vendor', request('pelanggan', '')));
        $filterLabel = request()->has('vendor')
            ? 'Penyedia'
            : (request()->has('pelanggan') ? 'Nama Pelanggan' : null);
    @endphp

    @if($filterLabel && $filterValue !== '')
        <div class="filter-banner">
            Menampilkan data untuk {{ $filterLabel }}: <strong>{{ $filterValue }}</strong>
            &nbsp;·&nbsp;
            <a href="{{ url()->current() }}">Tampilkan Semua Data</a>
        </div>
    @endif

    @php
        /*
        |--------------------------------------------------------------------
        | 1. Decode 5 baris pertama sebagai baris informasi & header Excel
        |    (mengikuti struktur asli file Excel: baris 1-2 = info,
        |     baris 3-5 = header tabel bertingkat, baris 6+ = data)
        |--------------------------------------------------------------------
        */

        $decode = function ($row) {
            $d = json_decode($row->row_data ?? '[]', true);
            return is_array($d) ? $d : [];
        };

        $trim = function ($v) {
            return trim((string) ($v ?? ''));
        };

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

        $infoRow1 = $rows->count() > 0 ? $decode($rows[0]) : [];
        $infoRow2 = $rows->count() > 1 ? $decode($rows[1]) : [];
        $L1       = $rows->count() > 2 ? $decode($rows[2]) : []; // baris judul kolom / paket
        $L2       = $rows->count() > 3 ? $decode($rows[3]) : []; // baris nama PIC (opsional)
        $L3       = $rows->count() > 4 ? $decode($rows[4]) : []; // baris sub kolom (REN/RESERVASI/dst)

        $totalCols = max(count($L1), count($L2), count($L3), 1);

        // TAMBAHAN: Deteksi kolom "Vendor" dan "Nama Pelanggan" dari baris
        // header tabel ini sendiri (L1/L2/L3), generik, berlaku juga di
        // sheet RES kalau kebetulan punya kolom tersebut. Dipakai untuk
        // fitur klik Vendor -> RES MFS dan klik Nama Pelanggan -> Detail KR 2026.
        $vendorCol    = null;
        $pelangganCol = null;

        foreach ([$L1, $L2, $L3] as $headerRowArr) {
            foreach ($headerRowArr as $colIdx => $headerVal) {
                $headerVal = mb_strtolower($trim($headerVal));
                if ($headerVal === '') {
                    continue;
                }
                if ($vendorCol === null && (str_contains($headerVal, 'vendor') || str_contains($headerVal, 'penyedia'))) {
                    $vendorCol = $colIdx;
                }
                if ($pelangganCol === null && str_contains($headerVal, 'pelanggan')) {
                    $pelangganCol = $colIdx;
                }
            }
        }

        // PERBAIKAN: Klik kolom Penyedia sekarang boleh ada selama ada
        // pemetaan Penyedia -> sheet RES miliknya ($resVendorMap tidak
        // kosong). Sheet tujuan yang tepat ditentukan per-baris (per nilai
        // Penyedia), bukan satu sheet tetap seperti sebelumnya.
        // PERBAIKAN: Vendor/Penyedia yang TIDAK memiliki sheet RES sendiri
        // tetap harus clickable (fallback ke DETAIL KR 2026 dengan filter
        // vendor - lihat blok $navLink di bawah), bukan hanya vendor yang
        // sudah ada di $resVendorMap.
        $canLinkVendor    = $vendorCol !== null && (!empty($resVendorMap) || ($detailKr2026SheetId && (int) $detailKr2026SheetId !== (int) $sheet->id));
        $canLinkPelanggan = $pelangganCol !== null && $detailKr2026SheetId && $detailKr2026SheetId != $sheet->id;

        // PERBAIKAN: Terapkan filter Penyedia/Nama Pelanggan (kalau ada) ke
        // baris data, hanya berdasarkan kolom yang memang sesuai. Sheet RES
        // per Penyedia (mis. "RES. MFS") umumnya TIDAK punya kolom Penyedia
        // per-baris karena satu sheet memang sudah mewakili satu Penyedia -
        // dalam kondisi ini filter tidak diterapkan supaya data tidak
        // hilang (sebelumnya ini menyebabkan halaman tampak kosong).
        $bodyRowsRes = $rows->skip(5);

        if ($filterValue !== '') {
            $filterCol = $filterLabel === 'Penyedia'
                ? $vendorCol
                : ($filterLabel === 'Nama Pelanggan' ? $pelangganCol : null);

            if ($filterCol !== null) {
                $bodyRowsRes = $bodyRowsRes->filter(function ($row) use ($decode, $trim, $filterValue, $filterCol) {
                    $cells = $decode($row);
                    return mb_strtolower($trim($cells[$filterCol] ?? '')) === mb_strtolower($filterValue);
                });
            }
        }

        /*
        |--------------------------------------------------------------------
        | 2. Deteksi baris info (PELAKSANA / KONTRAK) atau judul biasa
        |--------------------------------------------------------------------
        */

        $findLabelValue = function ($row) use ($trim) {
            foreach ($row as $idx => $val) {
                if ($trim($val) !== '') {
                    // cari value pada index setelahnya yang tidak kosong
                    $value = '';
                    for ($i = $idx + 1; $i < count($row); $i++) {
                        if ($trim($row[$i]) !== '') {
                            $value = $trim($row[$i]);
                            break;
                        }
                    }
                    $cleanLabel = rtrim($trim($val), " :\t\n\r\0\x0B");

                    return ['label' => $cleanLabel, 'value' => $value];
                }
            }
            return ['label' => '', 'value' => ''];
        };

        $info1 = $findLabelValue($infoRow1);
        $info2 = $findLabelValue($infoRow2);

        $isPelaksanaPattern =
            str_contains(mb_strtoupper($info1['label']), 'PELAKSANA') ||
            str_contains(mb_strtoupper($info2['label']), 'KONTRAK');

        /*
        |--------------------------------------------------------------------
        | 3. Bangun struktur header dinamis (2 atau 3 tingkat)
        |    berdasarkan pola pengisian L1 / L2 / L3
        |--------------------------------------------------------------------
        */

        $hasL3 = false;
        foreach ($L3 as $v) {
            if ($trim($v) !== '') { $hasL3 = true; break; }
        }

        $levels  = $hasL3 ? 3 : 2;
        $lastRow = $hasL3 ? $L3 : $L2;
        $midRow  = $hasL3 ? $L2 : null;

        // kelompokkan kolom berdasarkan baris paling atas (L1)
        $groups = [];
        $c = 0;
        while ($c < $totalCols) {
            $label = $trim($L1[$c] ?? '');
            $width = 1;
            $j = $c + 1;
            while ($j < $totalCols && $trim($L1[$j] ?? '') === '') {
                $width++;
                $j++;
            }

            $hasChildren = false;
            for ($k = $c; $k < $c + $width; $k++) {
                if ($trim($midRow[$k] ?? '') !== '' || $trim($lastRow[$k] ?? '') !== '') {
                    $hasChildren = true;
                    break;
                }
            }

            $groups[] = [
                'start'       => $c,
                'width'       => $width,
                'label'       => $label,
                'hasChildren' => $hasChildren,
            ];

            $c += $width;
        }

        // tentukan class css & lebar (px) tiap kolom "tetap" (standalone / bukan bagian paket)
        $widthMap = [
            'col-no'          => 46,
            'col-material'    => 300,
            'col-wbs'         => 80,
            'col-nomaterial'  => 130,
            'col-fixed'       => 100,
            'col-value'       => 62,
            'col-percent'     => 55,
        ];

        $colClass = [];
        $freezeColCount = null;

        foreach ($groups as $g) {
            if (!$g['hasChildren']) {
                $upper = mb_strtoupper($g['label']);
                if ($upper === 'NO' || $upper === 'NO.') {
                    $cls = 'col-no';
                } elseif (str_contains($upper, 'NO. MATERIAL') || str_contains($upper, 'NO.MATERIAL')) {
                    $cls = 'col-nomaterial';
                } elseif (str_contains($upper, 'MATERIAL')) {
                    $cls = 'col-material';
                } elseif (str_contains($upper, 'WBS')) {
                    $cls = 'col-wbs';
                } else {
                    $cls = 'col-fixed';
                }
                for ($k = $g['start']; $k < $g['start'] + $g['width']; $k++) {
                    $colClass[$k] = $cls;
                }
            } else {
                if ($freezeColCount === null) {
                    $freezeColCount = $g['start'];
                }
                for ($k = $g['start']; $k < $g['start'] + $g['width']; $k++) {
                    $leaf = $trim($lastRow[$k] ?? '');
                    $colClass[$k] = ($leaf === '%') ? 'col-percent' : 'col-value';
                }
            }
        }

        if ($freezeColCount === null) {
            $freezeColCount = $totalCols;
        }

        // hitung offset sticky-left kumulatif untuk kolom yang dibekukan (freeze pane)
        $colLeft = [];
        $accum = 0;
        for ($k = 0; $k < $freezeColCount; $k++) {
            $cls = $colClass[$k] ?? 'col-fixed';
            $colLeft[$k] = $accum;
            $accum += $widthMap[$cls] ?? 90;
        }

        // tinggi baris header (px) untuk perhitungan sticky-top bertingkat
        $headerRowHeight = 30;
    @endphp

    @if($isPelaksanaPattern)
        <div class="info-box">
            <div class="info-row">
                <div class="info-label">{{ $info1['label'] ?: 'PELAKSANA' }} :</div>
                <div class="info-value">{{ $info1['value'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $info2['label'] ?: 'KONTRAK' }} :</div>
                <div class="info-value">{{ $info2['value'] }}</div>
            </div>
        </div>
    @elseif($info1['label'] !== '')
        <div class="info-box">
            <div class="info-title">{{ $info1['label'] }}</div>
        </div>
    @endif

    <div class="table-box">
        <table>
            <thead>

                {{-- ================= BARIS HEADER 1 (TOP LEVEL) ================= --}}
                <tr>
                    @foreach($groups as $g)
                        @php
                            $cls = $colClass[$g['start']] ?? 'col-value';
                            $isFixed = !$g['hasChildren'];
                            $width = $widthMap[$cls] ?? 90;
                            $totalWidth = $width * $g['width'];
                            $rowspan = $isFixed ? $levels : 1;

                            $styleParts = ["width:{$totalWidth}px", "min-width:{$totalWidth}px"];
                            $stickyClass = '';

                            if ($g['start'] < $freezeColCount) {
                                $stickyClass = 'sticky-col';
                                $left = $colLeft[$g['start']] ?? 0;
                                $styleParts[] = "left:{$left}px";
                                $styleParts[] = "top:0px";
                            } else {
                                $styleParts[] = "top:0px";
                            }
                        @endphp

                        <th class="{{ $isFixed ? 'hdr-fixed' : 'hdr-paket' }} {{ $stickyClass }}"
                            style="{{ implode(';', $styleParts) }}"
                            colspan="{{ $g['width'] }}"
                            rowspan="{{ $rowspan }}">
                            {{ $g['label'] }}
                        </th>
                    @endforeach
                </tr>

                {{-- ================= BARIS HEADER 2 (NAMA PIC / MID LEVEL) ================= --}}
                @if($levels === 3)
                    <tr>
                        @foreach($groups as $g)
                            @continue(!$g['hasChildren'])

                            @php
                                // kelompokkan sub-header berdasarkan midRow di dalam rentang group ini
                                $subGroups = [];
                                $sc = $g['start'];
                                $end = $g['start'] + $g['width'];
                                while ($sc < $end) {
                                    $subLabel = $trim($midRow[$sc] ?? '');
                                    $subWidth = 1;
                                    $sj = $sc + 1;
                                    while ($sj < $end && $trim($midRow[$sj] ?? '') === '') {
                                        $subWidth++;
                                        $sj++;
                                    }
                                    $subGroups[] = ['start' => $sc, 'width' => $subWidth, 'label' => $subLabel];
                                    $sc += $subWidth;
                                }
                            @endphp

                            @foreach($subGroups as $sg)
                                @php
                                    $cls = $colClass[$sg['start']] ?? 'col-value';
                                    $width = ($widthMap[$cls] ?? 90) * $sg['width'];
                                @endphp
                                <th class="hdr-pic"
                                    style="width:{{ $width }}px;min-width:{{ $width }}px;top:{{ $headerRowHeight }}px;"
                                    colspan="{{ $sg['width'] }}">
                                    {{ $sg['label'] }}
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                @endif

                {{-- ================= BARIS HEADER TERAKHIR (LEAF: REN/RESERVASI/ISSUED/REAL/%) ================= --}}
                <tr>
                    @foreach($groups as $g)
                        @continue(!$g['hasChildren'])

                        @for($k = $g['start']; $k < $g['start'] + $g['width']; $k++)
                            @php
                                $cls = $colClass[$k] ?? 'col-value';
                                $width = $widthMap[$cls] ?? 90;
                                $top = $levels === 3 ? $headerRowHeight * 2 : $headerRowHeight;
                            @endphp
                            <th class="hdr-sub"
                                style="width:{{ $width }}px;min-width:{{ $width }}px;top:{{ $top }}px;">
                                {{ $trim($lastRow[$k] ?? '') }}
                            </th>
                        @endfor
                    @endforeach
                </tr>

            </thead>

            <tbody>
                @forelse($bodyRowsRes as $row)
                    @php
                        $data = $decode($row);

                        if (collect($data)->filter(fn($v) => $trim($v) !== '')->isEmpty()) {
                            continue;
                        }

                        $c0 = $trim($data[0] ?? '');
                        $c1 = $trim($data[1] ?? '');

                        $isCategory    = (bool) preg_match('/^[IVXLCDM]+\.?$/i', $c0) && $c1 !== '';
                        $isSubCategory = !$isCategory && (bool) preg_match('/^[a-z]{1,2}\.$/i', $c0) && $c1 !== '';
                        $isFooter      = !$isCategory && !$isSubCategory &&
                            (str_contains(mb_strtoupper($c0), 'STATUS') || str_contains(mb_strtoupper($c1), 'STATUS'));

                        $rowClass = $isCategory ? 'row-category' : ($isSubCategory ? 'row-subcategory' : ($isFooter ? 'row-footer' : ''));
                    @endphp

                    <tr class="{{ $rowClass }}" data-row="{{ $row->row_number }}">
                        @for($k = 0; $k < $totalCols; $k++)
                            @php
                                $cell = $trim($data[$k] ?? '');
                                $linkInfo = $detectLink($cell);

                                // PERBAIKAN: Link navigasi Penyedia -> sheet RES miliknya sendiri
                                // (mis. "PT Mega Family Sukses" -> "RES. MFS"), Nama Pelanggan -> Detail KR 2026
                                $navLink = null;
                                if (!$linkInfo && $cell !== '') {
                                    if ($canLinkVendor && $k === $vendorCol) {
                                        $targetResSheetId = $resVendorMap[\App\Http\Controllers\DashboardController::normalizeVendorKey($cell)] ?? null;
                                        if ($targetResSheetId && $targetResSheetId != $sheet->id) {
                                            $navLink = route('sheet.show', $targetResSheetId) . '?vendor=' . urlencode($cell);
                                        } elseif ($detailKr2026SheetId && (int) $detailKr2026SheetId !== (int) $sheet->id) {
                                            // PERBAIKAN: Vendor tidak punya sheet RES sendiri -> tetap
                                            // clickable, fallback ke DETAIL KR 2026 terfilter vendor ybs
                                            // (peringatan RES tidak ditemukan ditampilkan di halaman
                                            // DETAIL KR 2026 - lihat $vendorFallbackWarning di sheet.blade.php).
                                            $navLink = route('sheet.show', $detailKr2026SheetId) . '?vendor=' . urlencode($cell);
                                        }
                                    } elseif ($canLinkPelanggan && $k === $pelangganCol) {
                                        $navLink = route('sheet.show', $detailKr2026SheetId) . '?pelanggan=' . urlencode($cell);
                                    }
                                }

                                $cls = $colClass[$k] ?? 'col-value';

                                // PERBAIKAN: alignment isi tabel dibuat KONSISTEN -
                                // SEMUA cell rata kiri (sebelumnya kolom col-value rata
                                // kanan dan kolom lain rata tengah). vertical-align:middle
                                // sudah diatur secara global lewat CSS "tbody td".
                                $align = 'text-left';

                                $isCatCell = ($isCategory || $isSubCategory) && $k <= 1;

                                $tdStyleParts = [];
                                $stickyClass = '';

                                if ($k < $freezeColCount) {
                                    $stickyClass = 'sticky-col';
                                    $left = $colLeft[$k] ?? 0;
                                    $tdStyleParts[] = "left:{$left}px";
                                }
                            @endphp

                            <td class="{{ $align }} {{ $stickyClass }} {{ $isCatCell ? 'cat-cell' : '' }}"
                                style="{{ implode(';', $tdStyleParts) }}">
                                @if($linkInfo)
                                    <a href="{{ $cell }}" target="_blank" rel="noopener noreferrer" class="cell-link-btn">{{ $linkInfo['icon'] }} {{ $linkInfo['label'] }}</a>
                                @elseif($navLink)
                                    <a href="{{ $navLink }}" class="cell-nav-link">{{ $cell }}</a>
                                @else
                                    {{ $cell }}
                                @endif
                            </td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $totalCols }}" class="empty-state">
                            Tidak ada data untuk ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

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