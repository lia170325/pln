<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sheet->nama_sheet }}</title>

    {{--
        ============================================================
        VIEW KHUSUS & TERISOLASI: Monitoring Pelanggan -> PELANGGAN 2026
        ============================================================
        View ini HANYA dipakai untuk sheet "PELANGGAN 2026" (lihat
        percabangan di DashboardController@show). Semua CSS di bawah
        diberi prefix "plg2026-" dan didefinisikan di dalam file ini
        sendiri (tidak memakai/mengubah class atau file CSS yang
        dipakai sheet lain), supaya perubahan tampilan di sini TIDAK
        berdampak ke Monitoring KHS, Monitoring Tiang, RES, dst.

        Struktur header (baris 1-4: nomor kolom, judul besar, grup,
        sub-judul/nama kolom) beserta lebar setiap kolom diambil PERSIS
        dari hasil pembacaan merge-cell & lebar kolom pada spreadsheet
        acuan "PELANGGAN 2026" (posisi, colspan, rowspan, dan warna
        header sesuai aslinya). Data yang ditampilkan tetap 100% dari
        data yang sudah tersimpan/terimport (SheetData) - tidak ada
        data dummy dan tidak ada isi data yang diubah.
    --}}

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        .plg2026-page{
            font-family:Calibri, Arial, Helvetica, sans-serif;
            background:#f5f7fb;
        }

        .plg2026-container{
            width:98%;
            margin:20px auto 40px auto;
        }

        /* ================= PAGE HEADER (judul halaman + tombol) ================= */

        .plg2026-page-header{
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

        .plg2026-page-header h2{
            font-size:24px;
            line-height:1.3;
        }

        .plg2026-page-header p{
            font-size:13px;
            opacity:.9;
        }

        .plg2026-btn{
            background:#fff;
            color:#0B5EA8;
            text-decoration:none;
            padding:10px 20px;
            border-radius:8px;
            font-weight:bold;
            font-size:14px;
            white-space:nowrap;
        }

        .plg2026-btn:hover{
            background:#e9ecef;
        }

        .plg2026-header-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }

        .plg2026-hint{
            background:#fff;
            border:1px solid #e2e6ea;
            border-radius:10px;
            padding:10px 16px;
            margin-bottom:14px;
            font-size:12.5px;
            color:#555;
        }

        /* ================= TABLE WRAPPER: scroll horizontal (dan vertikal
           mengikuti scroll halaman), sekarang DENGAN sticky/freeze header,
           mengikuti pola layout tabel yang sama dengan sheet lain (mis.
           KHS JASA 2026 / sheet.blade.php). Header tabel ini punya 4 baris
           dengan banyak sel rowspan/tinggi baris yang tidak seragam (baris
           1 kecil, ada label yang boleh turun ke beberapa baris), sehingga
           posisi "top" tiap baris header TIDAK di-hardcode di CSS (itu
           yang dulu memicu bug tinggi baris raksasa) - posisinya dihitung
           otomatis lewat JS (lihat <script> di bawah, fungsi
           applyStickyHeaderRows) berdasarkan tinggi baris yang SUNGGUHAN
           dirender, supaya selalu presisi walau ukuran teks/kolom berubah.
           Freeze kolom (NO & NAMA PELANGGAN) yang sudah ada sebelumnya
           tetap dipertahankan seperti semula. ================= */

        .plg2026-table-box{
            background:#fff;
            border-radius:10px;
            overflow-x:auto;
            overflow-y:visible;
            position:relative;
            box-shadow:0 2px 8px rgba(11,94,168,.10);
            border:1px solid #cdd9e6;
        }

        .plg2026-table{
            border-collapse:collapse;
            table-layout:fixed;
            /* PENTING: paksa tabel memakai lebar aslinya (jumlah lebar
               semua kolom di colgroup), BUKAN diperas masuk ke lebar
               container. Tanpa ini, browser bisa memampatkan puluhan
               kolom ke dalam layar sampai sangat sempit, yang lalu
               memaksa teks header terpotong huruf-per-huruf ke bawah. */
            width:max-content;
            min-width:100%;
        }

        .plg2026-table th,
        .plg2026-table td{
            border:1px solid #dde4ec;
            padding:5px 8px;
            font-size:12px;
            line-height:1.35;
            /* PERBAIKAN: teks panjang sekarang turun ke baris berikutnya
               dengan rapi (word-wrap), TIDAK lagi dipotong dengan "..."
               (overflow:hidden/text-overflow:ellipsis dihapus). Scroll
               horizontal tabel (.plg2026-table-box{overflow-x:auto}) tetap
               dipertahankan seperti semula untuk kolom yang banyak. */
            white-space:normal;
            word-break:normal;
            overflow-wrap:break-word;
            vertical-align:middle;
        }

        .plg2026-table thead th{
            background:#0B5EA8;
            color:#fff;
            font-weight:bold;
            text-align:center;
            vertical-align:middle;
            /* Teks header boleh turun ke 2-3 baris kalau panjang, tapi
               HARUS tetap memenggal di batas kata (spasi), bukan di
               tengah kata/huruf. word-break:normal + overflow-wrap
               hanya memaksa potong kalau ada satu "kata" yang memang
               lebih panjang dari lebar kolom. */
            white-space:normal;
            word-break:normal;
            overflow-wrap:break-word;
            /* TAMBAHAN: freeze/sticky header - nilai "top" diisi otomatis
               lewat JS (applyStickyHeaderRows) sesuai tinggi baris header
               yang sungguhan dirender, supaya 4 baris header ini tetap
               berurutan rapi (tidak ada header ganda/tumpang tindih) saat
               halaman di-scroll ke bawah. */
            position:sticky;
            z-index:5;
        }

        /* baris nomor kolom (baris 1 Excel) */
        .plg2026-row-colnum th{
            font-size:9px;
            font-weight:normal;
            padding:1px 3px;
        }

        .plg2026-table tbody td.text-left{ text-align:left; }
        .plg2026-table tbody td.text-right{ text-align:right; }
        .plg2026-table tbody td.text-center{ text-align:center; }

        .plg2026-table tbody tr:nth-child(even){ background:#fafbfd; }
        .plg2026-table tbody tr:hover{ background:#eef5ff; }

        .plg2026-table tbody tr.plg2026-row-highlight{
            outline:2px solid #0B5EA8;
            outline-offset:-2px;
        }
        .plg2026-table tbody tr.plg2026-row-highlight td{
            background:#eaf3fc;
        }

        /* ================= TAMBAHAN: freeze kolom NO & NAMA PELANGGAN
           supaya tetap terlihat saat tabel di-scroll horizontal. Hanya
           berlaku untuk sel header yang memang 1 kolom (colspan=1) -
           sel judul gabungan seperti "MONITORING PERMOHONAN TAHUN 2026"
           yang meng-colspan banyak kolom sekaligus tidak di-freeze,
           karena bagian dari satu sel tidak bisa dibuat sticky sebagian
           tanpa memecah merge cell aslinya. Ini murni tambahan tampilan,
           tidak mengubah ukuran, warna, atau layout yang sudah ada. ================= */

        .plg2026-table .plg2026-freeze-1,
        .plg2026-table .plg2026-freeze-2{
            position:sticky;
            z-index:2;
            background:#fff;
        }
        .plg2026-table .plg2026-freeze-1{ left:0; }
        .plg2026-table thead .plg2026-freeze-1,
        .plg2026-table thead .plg2026-freeze-2{ z-index:6; }

        .plg2026-table tbody tr:nth-child(even) .plg2026-freeze-1,
        .plg2026-table tbody tr:nth-child(even) .plg2026-freeze-2{ background:#fafbfd; }
        .plg2026-table tbody tr:hover .plg2026-freeze-1,
        .plg2026-table tbody tr:hover .plg2026-freeze-2{ background:#eef5ff; }
        .plg2026-table tbody tr.plg2026-row-highlight .plg2026-freeze-1,
        .plg2026-table tbody tr.plg2026-row-highlight .plg2026-freeze-2{ background:#eaf3fc; }

        /* PERBAIKAN: kolom NO sekarang rata kiri juga, konsisten dengan
           seluruh isi tabel lainnya (vertical-align tetap middle). */
        .plg2026-table tbody td.plg2026-col-no{
            text-align:left;
            vertical-align:middle;
        }

        .plg2026-empty-state{
            padding:40px;
            text-align:center;
            color:#888;
            font-size:14px;
        }

        @media (max-width:768px){
            .plg2026-page-header h2{ font-size:19px; }
            .plg2026-page-header p{ font-size:12px; }
            .plg2026-btn{ padding:8px 14px; font-size:12px; }
            .plg2026-table th, .plg2026-table td{ font-size:11px; padding:4px 6px; }
        }

    </style>
</head>

<body class="plg2026-page">

<div class="plg2026-container">

    <div class="plg2026-page-header">
        <div>
            <h2>{{ $sheet->nama_sheet }}</h2>
            <p>Total Baris : {{ $sheet->total_rows }}</p>
        </div>

        @php
            $dashboardRoute = route('dashboard');
        @endphp

        <div class="plg2026-header-actions">
            <a href="{{ $dashboardRoute }}" class="plg2026-btn" id="plg2026BtnBackHistory">
                &larr; Kembali
            </a>
            <a href="{{ $dashboardRoute }}" class="plg2026-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px; margin-right:4px;">
                    <path d="M3 11.5 12 4l9 7.5"></path>
                    <path d="M5.5 10v9a1 1 0 0 0 1 1H9.5a1 1 0 0 0 1-1V15a1 1 0 0 1 1-1H12.5a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1H17.5a1 1 0 0 0 1-1v-9"></path>
                </svg>Dashboard
            </a>
        </div>
    </div>

    <div class="plg2026-hint">
        Geser tabel ke kanan untuk melihat kolom lainnya (kolom cukup banyak), dan scroll halaman ke bawah untuk melihat seluruh baris data.
    </div>

    @php
        $decode = function ($row) {
            $d = json_decode($row->row_data ?? '[]', true);
            return is_array($d) ? $d : [];
        };
        $trim = function ($v) {
            return trim((string) ($v ?? ''));
        };

        // Header PELANGGAN 2026 - hasil ekstraksi PERSIS dari merge cell
        // spreadsheet asli (baris 1-4), dipakai HANYA oleh sheet ini.
        $headerCells = [
            ['r'=>1,'c'=>1,'rs'=>1,'cs'=>1,'label'=>'1','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>2,'rs'=>1,'cs'=>1,'label'=>'2','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>3,'rs'=>1,'cs'=>1,'label'=>'3','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>4,'rs'=>1,'cs'=>1,'label'=>'4','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>5,'rs'=>1,'cs'=>1,'label'=>'5','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>6,'rs'=>1,'cs'=>1,'label'=>'6','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>7,'rs'=>1,'cs'=>1,'label'=>'7','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>8,'rs'=>1,'cs'=>1,'label'=>'8','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>9,'rs'=>1,'cs'=>1,'label'=>'9','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>10,'rs'=>1,'cs'=>1,'label'=>'10','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>11,'rs'=>1,'cs'=>1,'label'=>'11','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>12,'rs'=>1,'cs'=>1,'label'=>'12','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>13,'rs'=>1,'cs'=>1,'label'=>'13','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>14,'rs'=>1,'cs'=>1,'label'=>'14','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>15,'rs'=>1,'cs'=>1,'label'=>'15','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>16,'rs'=>1,'cs'=>1,'label'=>'16','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>17,'rs'=>1,'cs'=>1,'label'=>'17','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>18,'rs'=>1,'cs'=>1,'label'=>'18','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>19,'rs'=>1,'cs'=>1,'label'=>'19','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>20,'rs'=>1,'cs'=>1,'label'=>'20','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>21,'rs'=>1,'cs'=>1,'label'=>'21','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>1,'c'=>22,'rs'=>1,'cs'=>1,'label'=>'22','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>23,'rs'=>1,'cs'=>1,'label'=>'23','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>24,'rs'=>1,'cs'=>1,'label'=>'24','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>25,'rs'=>1,'cs'=>1,'label'=>'25','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>26,'rs'=>1,'cs'=>1,'label'=>'26','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>27,'rs'=>1,'cs'=>1,'label'=>'27','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>28,'rs'=>1,'cs'=>1,'label'=>'28','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>29,'rs'=>1,'cs'=>1,'label'=>'29','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>30,'rs'=>1,'cs'=>1,'label'=>'30','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>31,'rs'=>1,'cs'=>1,'label'=>'31','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>32,'rs'=>1,'cs'=>1,'label'=>'32','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>33,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#000000','wrap'=>false],
            ['r'=>1,'c'=>34,'rs'=>1,'cs'=>1,'label'=>'33','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>35,'rs'=>1,'cs'=>1,'label'=>'34','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>36,'rs'=>1,'cs'=>1,'label'=>'35','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>37,'rs'=>1,'cs'=>1,'label'=>'36','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>38,'rs'=>1,'cs'=>1,'label'=>'37','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>39,'rs'=>1,'cs'=>1,'label'=>'38','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>40,'rs'=>1,'cs'=>1,'label'=>'39','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>41,'rs'=>1,'cs'=>1,'label'=>'40','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>42,'rs'=>1,'cs'=>1,'label'=>'41','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>43,'rs'=>1,'cs'=>1,'label'=>'42','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>44,'rs'=>1,'cs'=>1,'label'=>'43','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>45,'rs'=>1,'cs'=>1,'label'=>'44','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>46,'rs'=>1,'cs'=>1,'label'=>'45','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>47,'rs'=>1,'cs'=>1,'label'=>'46','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>48,'rs'=>1,'cs'=>1,'label'=>'47','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>49,'rs'=>1,'cs'=>1,'label'=>'48','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>50,'rs'=>1,'cs'=>1,'label'=>'49','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>51,'rs'=>1,'cs'=>1,'label'=>'50','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>52,'rs'=>1,'cs'=>1,'label'=>'51','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>53,'rs'=>1,'cs'=>1,'label'=>'52','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>54,'rs'=>1,'cs'=>1,'label'=>'53','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>55,'rs'=>1,'cs'=>1,'label'=>'54','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>56,'rs'=>1,'cs'=>1,'label'=>'55','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>57,'rs'=>1,'cs'=>1,'label'=>'56','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>58,'rs'=>1,'cs'=>1,'label'=>'57','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>59,'rs'=>1,'cs'=>1,'label'=>'58','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>60,'rs'=>1,'cs'=>1,'label'=>'59','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>61,'rs'=>1,'cs'=>1,'label'=>'60','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>62,'rs'=>1,'cs'=>1,'label'=>'61','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>63,'rs'=>1,'cs'=>1,'label'=>'62','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>64,'rs'=>1,'cs'=>1,'label'=>'63','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>65,'rs'=>1,'cs'=>1,'label'=>'64','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>66,'rs'=>1,'cs'=>1,'label'=>'65','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>67,'rs'=>1,'cs'=>1,'label'=>'66','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>68,'rs'=>1,'cs'=>1,'label'=>'67','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>69,'rs'=>1,'cs'=>1,'label'=>'68','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>70,'rs'=>1,'cs'=>1,'label'=>'69','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>71,'rs'=>1,'cs'=>1,'label'=>'70','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>72,'rs'=>1,'cs'=>1,'label'=>'71','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>73,'rs'=>1,'cs'=>1,'label'=>'72','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>74,'rs'=>1,'cs'=>1,'label'=>'73','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>75,'rs'=>1,'cs'=>1,'label'=>'74','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>76,'rs'=>1,'cs'=>1,'label'=>'75','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>77,'rs'=>1,'cs'=>1,'label'=>'76','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>78,'rs'=>1,'cs'=>1,'label'=>'77','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>79,'rs'=>1,'cs'=>1,'label'=>'78','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>80,'rs'=>1,'cs'=>1,'label'=>'79','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>81,'rs'=>1,'cs'=>1,'label'=>'80','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>82,'rs'=>1,'cs'=>1,'label'=>'81','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>83,'rs'=>1,'cs'=>1,'label'=>'82','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>84,'rs'=>1,'cs'=>1,'label'=>'83','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>85,'rs'=>1,'cs'=>1,'label'=>'84','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>86,'rs'=>1,'cs'=>1,'label'=>'85','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>87,'rs'=>1,'cs'=>1,'label'=>'86','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>88,'rs'=>1,'cs'=>1,'label'=>'87','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>89,'rs'=>1,'cs'=>1,'label'=>'88','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>90,'rs'=>1,'cs'=>1,'label'=>'89','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>91,'rs'=>1,'cs'=>1,'label'=>'90','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>92,'rs'=>1,'cs'=>1,'label'=>'91','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>93,'rs'=>1,'cs'=>1,'label'=>'92','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>94,'rs'=>1,'cs'=>1,'label'=>'93','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>95,'rs'=>1,'cs'=>1,'label'=>'94','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>1,'c'=>96,'rs'=>1,'cs'=>1,'label'=>'95','bg'=>'#3F3F3F','wrap'=>false],
            ['r'=>2,'c'=>1,'rs'=>2,'cs'=>14,'label'=>'MONITORING PERMOHONAN TAHUN 2026','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>15,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#FEF2CB','wrap'=>false],
            ['r'=>2,'c'=>16,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#FEF2CB','wrap'=>false],
            ['r'=>2,'c'=>17,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#D9E2F3','wrap'=>false],
            ['r'=>2,'c'=>18,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#D9E2F3','wrap'=>false],
            ['r'=>2,'c'=>19,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#C5E0B3','wrap'=>false],
            ['r'=>2,'c'=>20,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#C5E0B3','wrap'=>false],
            ['r'=>2,'c'=>21,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>2,'c'=>22,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>23,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>24,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>25,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>26,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>27,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>28,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#FFE598','wrap'=>false],
            ['r'=>2,'c'=>29,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#FFE598','wrap'=>false],
            ['r'=>2,'c'=>30,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#BDD6EE','wrap'=>false],
            ['r'=>2,'c'=>31,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#BDD6EE','wrap'=>false],
            ['r'=>2,'c'=>32,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>2,'c'=>33,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#000000','wrap'=>false],
            ['r'=>2,'c'=>34,'rs'=>1,'cs'=>9,'label'=>'HASIL KAJIAN FINANSIAL (KF)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>2,'c'=>43,'rs'=>1,'cs'=>13,'label'=>'KONSTRUKSI TM','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>2,'c'=>56,'rs'=>1,'cs'=>3,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>59,'rs'=>1,'cs'=>3,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>62,'rs'=>1,'cs'=>6,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>68,'rs'=>1,'cs'=>4,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>72,'rs'=>1,'cs'=>5,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>77,'rs'=>1,'cs'=>5,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>82,'rs'=>1,'cs'=>2,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>84,'rs'=>1,'cs'=>3,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>87,'rs'=>1,'cs'=>2,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>2,'c'=>89,'rs'=>1,'cs'=>8,'label'=>'MATERIAL DISTRIBUSI UTAMA (MDU)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>15,'rs'=>1,'cs'=>2,'label'=>'SURAT PELANGGAN / ULP','bg'=>'#FEF2CB','wrap'=>false],
            ['r'=>3,'c'=>17,'rs'=>1,'cs'=>2,'label'=>'NODIN SAR - REN (KF)','bg'=>'#D9E2F3','wrap'=>false],
            ['r'=>3,'c'=>19,'rs'=>1,'cs'=>2,'label'=>'NODIN REN - SAR (HASIL KF)','bg'=>'#C5E0B3','wrap'=>false],
            ['r'=>3,'c'=>21,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>22,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>3,'c'=>23,'rs'=>1,'cs'=>2,'label'=>'SURAT BALASAN','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>3,'c'=>25,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>3,'c'=>26,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>3,'c'=>27,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>3,'c'=>28,'rs'=>1,'cs'=>2,'label'=>'KONTRAK RINCI','bg'=>'#FFE598','wrap'=>false],
            ['r'=>3,'c'=>30,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#BDD6EE','wrap'=>false],
            ['r'=>3,'c'=>31,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#BDD6EE','wrap'=>false],
            ['r'=>3,'c'=>32,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>3,'c'=>33,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#000000','wrap'=>false],
            ['r'=>3,'c'=>34,'rs'=>2,'cs'=>1,'label'=>'AI 
(MDU+JASA)
(Rp)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>3,'c'=>35,'rs'=>2,'cs'=>1,'label'=>'AI 
(JASA)
(Rp)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>3,'c'=>36,'rs'=>1,'cs'=>4,'label'=>'MINIMUM','bg'=>'#FF0000','wrap'=>false],
            ['r'=>3,'c'=>40,'rs'=>1,'cs'=>3,'label'=>'EXPECTED','bg'=>'#FF0000','wrap'=>false],
            ['r'=>3,'c'=>43,'rs'=>2,'cs'=>1,'label'=>'TM 1','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>44,'rs'=>2,'cs'=>1,'label'=>'TM 3','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>45,'rs'=>2,'cs'=>1,'label'=>'TM 4','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>46,'rs'=>2,'cs'=>1,'label'=>'TM 4X','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>47,'rs'=>2,'cs'=>1,'label'=>'TM 5','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>48,'rs'=>2,'cs'=>1,'label'=>'TM 8','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>49,'rs'=>2,'cs'=>1,'label'=>'TM 8X','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>50,'rs'=>2,'cs'=>1,'label'=>'TM 10','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>51,'rs'=>2,'cs'=>1,'label'=>'TM TYPE 1','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>3,'c'=>52,'rs'=>2,'cs'=>1,'label'=>'TM TYPE 2','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>3,'c'=>53,'rs'=>2,'cs'=>1,'label'=>'TM TYPE 3','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>3,'c'=>54,'rs'=>2,'cs'=>1,'label'=>'GW','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>55,'rs'=>2,'cs'=>1,'label'=>'HGW','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>3,'c'=>56,'rs'=>1,'cs'=>3,'label'=>'Tiang Beton','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>59,'rs'=>1,'cs'=>3,'label'=>'Tiang Besi','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>62,'rs'=>1,'cs'=>6,'label'=>'Kabel TM (mtr)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>68,'rs'=>1,'cs'=>4,'label'=>'Isolator (bh)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>72,'rs'=>1,'cs'=>1,'label'=>'Arester','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>73,'rs'=>1,'cs'=>1,'label'=>'FCO','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>74,'rs'=>1,'cs'=>2,'label'=>'Kubikel (cell)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>76,'rs'=>1,'cs'=>1,'label'=>'CT TM (set)
UNTUK PD TM','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>77,'rs'=>1,'cs'=>5,'label'=>'Trafo (bh)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>82,'rs'=>1,'cs'=>2,'label'=>'Kabel TR (mtr)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>84,'rs'=>1,'cs'=>3,'label'=>'PHBTR (bh)','bg'=>'#FF0000','wrap'=>true],
            ['r'=>3,'c'=>87,'rs'=>1,'cs'=>2,'label'=>'NYY (mtr)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>3,'c'=>89,'rs'=>1,'cs'=>8,'label'=>'Box APP Pengukuran Tak Langsung (TR)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>1,'rs'=>1,'cs'=>1,'label'=>'NO','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>2,'rs'=>1,'cs'=>1,'label'=>'NAMA PELANGGAN','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>3,'rs'=>1,'cs'=>1,'label'=>'ALAMAT  PELANGGAN','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>4,'rs'=>1,'cs'=>1,'label'=>'ULP','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>5,'rs'=>1,'cs'=>1,'label'=>'JENIS PERMOHOAN','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>4,'c'=>6,'rs'=>1,'cs'=>1,'label'=>'JUMLAH PLG','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>4,'c'=>7,'rs'=>1,'cs'=>1,'label'=>'TARIF','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>8,'rs'=>1,'cs'=>1,'label'=>'AWAL','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>9,'rs'=>1,'cs'=>1,'label'=>'AKHIR','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>10,'rs'=>1,'cs'=>1,'label'=>'JENIS PELANGGAN','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>4,'c'=>11,'rs'=>1,'cs'=>1,'label'=>'TYPE PELANGGAN','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>12,'rs'=>1,'cs'=>1,'label'=>'TOTAL DAYA','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>13,'rs'=>1,'cs'=>1,'label'=>'POTENSI BP
(Rp)','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>14,'rs'=>1,'cs'=>1,'label'=>'PENYULANG','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>15,'rs'=>1,'cs'=>1,'label'=>'NOMOR SURAT PELANGGAN / ULP','bg'=>'#FEF2CB','wrap'=>false],
            ['r'=>4,'c'=>16,'rs'=>1,'cs'=>1,'label'=>'TANGGAL','bg'=>'#FEF2CB','wrap'=>false],
            ['r'=>4,'c'=>17,'rs'=>1,'cs'=>1,'label'=>'NOMOR NODIN SAR - REN (KF)','bg'=>'#D9E2F3','wrap'=>false],
            ['r'=>4,'c'=>18,'rs'=>1,'cs'=>1,'label'=>'TANGGAL','bg'=>'#D9E2F3','wrap'=>false],
            ['r'=>4,'c'=>19,'rs'=>1,'cs'=>1,'label'=>'NOMOR NODIN REN - SAR (HASIL KF)','bg'=>'#C5E0B3','wrap'=>false],
            ['r'=>4,'c'=>20,'rs'=>1,'cs'=>1,'label'=>'TANGGAL','bg'=>'#C5E0B3','wrap'=>false],
            ['r'=>4,'c'=>21,'rs'=>1,'cs'=>1,'label'=>'STATUS PROGRES','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>22,'rs'=>1,'cs'=>1,'label'=>'KETERANGAN','bg'=>'#F7CAAC','wrap'=>false],
            ['r'=>4,'c'=>23,'rs'=>1,'cs'=>1,'label'=>'NO SURAT','bg'=>'#FFFF00','wrap'=>true],
            ['r'=>4,'c'=>24,'rs'=>1,'cs'=>1,'label'=>'TANGGAL SURAT','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>25,'rs'=>1,'cs'=>1,'label'=>'STATUS PEMBAYARAN','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>26,'rs'=>1,'cs'=>1,'label'=>'TGL PEMBAYARAN','bg'=>'#FFFF00','wrap'=>false],
            ['r'=>4,'c'=>27,'rs'=>1,'cs'=>1,'label'=>'PROGRES PEMESANAN TIANG','bg'=>'#F7CAAC','wrap'=>true],
            ['r'=>4,'c'=>28,'rs'=>1,'cs'=>1,'label'=>'NOMOR NODIN SAR - REN (NODIN KR)','bg'=>'#FFE598','wrap'=>true],
            ['r'=>4,'c'=>29,'rs'=>1,'cs'=>1,'label'=>'TANGGAL','bg'=>'#FFE598','wrap'=>false],
            ['r'=>4,'c'=>30,'rs'=>1,'cs'=>1,'label'=>'NOMOR NODIN REN - MUP3 (NODIN KR)','bg'=>'#BDD6EE','wrap'=>true],
            ['r'=>4,'c'=>31,'rs'=>1,'cs'=>1,'label'=>'TANGGAL','bg'=>'#BDD6EE','wrap'=>false],
            ['r'=>4,'c'=>32,'rs'=>1,'cs'=>1,'label'=>'STATUS PDL','bg'=>'#F7CAAC','wrap'=>true],
            ['r'=>4,'c'=>33,'rs'=>1,'cs'=>1,'label'=>'','bg'=>'#000000','wrap'=>false],
            ['r'=>4,'c'=>36,'rs'=>1,'cs'=>1,'label'=>'rWACC','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>37,'rs'=>1,'cs'=>1,'label'=>'IRR','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>38,'rs'=>1,'cs'=>1,'label'=>'NPV 
(Rp Juta)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>39,'rs'=>1,'cs'=>1,'label'=>'PBP','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>40,'rs'=>1,'cs'=>1,'label'=>'IRR','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>41,'rs'=>1,'cs'=>1,'label'=>'NPV 
(Rp Juta)','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>42,'rs'=>1,'cs'=>1,'label'=>'PBP','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>56,'rs'=>1,'cs'=>1,'label'=>'Tiang Beton 9m','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>57,'rs'=>1,'cs'=>1,'label'=>'Tiang Beton 12m','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>58,'rs'=>1,'cs'=>1,'label'=>'Tiang Beton 14m','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>59,'rs'=>1,'cs'=>1,'label'=>'Tiang Besi 9m','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>60,'rs'=>1,'cs'=>1,'label'=>'Tiang Besi 12m','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>61,'rs'=>1,'cs'=>1,'label'=>'Tiang Besi 14m','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>62,'rs'=>1,'cs'=>1,'label'=>'Kabel TM MVTIC','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>63,'rs'=>1,'cs'=>1,'label'=>'Kabel TM SKTM 150','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>64,'rs'=>1,'cs'=>1,'label'=>'Kabel TM SKTM 240','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>65,'rs'=>1,'cs'=>1,'label'=>'Kabel TM A3CS 70','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>66,'rs'=>1,'cs'=>1,'label'=>'Kabel TM A3CS 150','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>67,'rs'=>1,'cs'=>1,'label'=>'Kabel TM A3CS 240','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>68,'rs'=>1,'cs'=>1,'label'=>'Isolator Tumpu Porselaian','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>69,'rs'=>1,'cs'=>1,'label'=>'Isolator Tumpu Polymer','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>70,'rs'=>1,'cs'=>1,'label'=>'Isolator Tarik Porselaian','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>71,'rs'=>1,'cs'=>1,'label'=>'Isolator Tarik Polymer','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>72,'rs'=>1,'cs'=>1,'label'=>'Arester','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>73,'rs'=>1,'cs'=>1,'label'=>'FCO','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>74,'rs'=>1,'cs'=>1,'label'=>'Kubikel Incoming','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>75,'rs'=>1,'cs'=>1,'label'=>'Kubikel Outgoing','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>76,'rs'=>1,'cs'=>1,'label'=>'CT TM (set)
UNTUK PD TM','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>77,'rs'=>1,'cs'=>1,'label'=>'Trafo 400 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>78,'rs'=>1,'cs'=>1,'label'=>'Trafo 250 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>79,'rs'=>1,'cs'=>1,'label'=>'Trafo 160 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>80,'rs'=>1,'cs'=>1,'label'=>'Trafo 100 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>81,'rs'=>1,'cs'=>1,'label'=>'Trafo 50 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>82,'rs'=>1,'cs'=>1,'label'=>'Kabel TR 3x70mm','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>83,'rs'=>1,'cs'=>1,'label'=>'Kabel TR 3x35mm','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>84,'rs'=>1,'cs'=>1,'label'=>'PHBTR 250A - 2 LINE','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>85,'rs'=>1,'cs'=>1,'label'=>'PHBTR 400A - 2 LINE','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>86,'rs'=>1,'cs'=>1,'label'=>'PHBTR 400A - 4 LINE','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>87,'rs'=>1,'cs'=>1,'label'=>'NYY 70mm','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>88,'rs'=>1,'cs'=>1,'label'=>'NYY 150mm','bg'=>'#FF0000','wrap'=>false],
            ['r'=>4,'c'=>89,'rs'=>1,'cs'=>1,'label'=>'Box APP 53 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>90,'rs'=>1,'cs'=>1,'label'=>'Box APP 66 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>91,'rs'=>1,'cs'=>1,'label'=>'Box APP 82,5 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>92,'rs'=>1,'cs'=>1,'label'=>'Box APP 105 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>93,'rs'=>1,'cs'=>1,'label'=>'Box APP 131 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>94,'rs'=>1,'cs'=>1,'label'=>'Box APP 147 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>95,'rs'=>1,'cs'=>1,'label'=>'Box APP 164 KVA','bg'=>'#FF0000','wrap'=>true],
            ['r'=>4,'c'=>96,'rs'=>1,'cs'=>1,'label'=>'Box APP 197 KVA','bg'=>'#FF0000','wrap'=>true],
        ];

        // Baris data = seluruh baris dengan row_number > 5 (baris 1-4 header,
        // baris 5 kosong sebagai pemisah, persis posisi aslinya di Excel).
        $dataRows = [];
        foreach ($rows as $row) {
            if ($row->row_number <= 5) continue;
            $cells = $decode($row);
            $isBlank = true;
            foreach ($cells as $v) {
                if ($trim($v) !== '') { $isBlank = false; break; }
            }
            if ($isBlank) continue;
            $dataRows[] = ['number' => $row->row_number, 'cells' => $cells];
        }

        $totalCols = 96;
        foreach ($dataRows as $r) {
            $totalCols = max($totalCols, count($r['cells']));
        }

        // Susun ulang $headerCells jadi grid per baris (1-4) supaya gampang
        // dirender per <tr>, dan lengkapi kolom di luar hasil ekstraksi
        // (mis. kalau suatu saat kolom bertambah) dengan sel kosong 1x1.
        $headerByRow = [1 => [], 2 => [], 3 => [], 4 => []];
        foreach ($headerCells as $hc) {
            $headerByRow[$hc['r']][] = $hc;
        }
        foreach ($headerByRow as $rr => $cellsInRow) {
            usort($headerByRow[$rr], fn($a, $b) => $a['c'] <=> $b['c']);
        }

        // Lebar kolom (px) dihitung dari PANJANG LABEL header aslinya
        // (bukan lagi dari lebar kolom mentah file Excel, yang sering
        // jauh lebih kecil dari label sebenarnya - itulah yang membuat
        // teks header terpaksa terpotong huruf-per-huruf). Hanya label
        // milik SATU kolom (colspan=1) yang dipakai, supaya judul grup
        // gabungan (colspan>1, mis. "MATERIAL DISTRIBUSI UTAMA (MDU)")
        // tidak memaksa satu kolom jadi sangat lebar - lebar kolom
        // gabungan itu tetap mengikuti total lebar kolom-kolom di
        // bawahnya (baris 4), sama seperti tabel Excel aslinya.
        $longestLabelForCol = [];
        foreach ($headerCells as $hc) {
            if ($hc['cs'] > 1) continue;
            $label = trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $hc['label'])));
            if ($label === '') continue;
            $c = $hc['c'];
            if (!isset($longestLabelForCol[$c]) || mb_strlen($label) > mb_strlen($longestLabelForCol[$c])) {
                $longestLabelForCol[$c] = $label;
            }
        }

        $colWidthPx = function ($c) use ($longestLabelForCol) {
            // Kolom NO (kolom 1) dibuat kecil & tetap - cukup untuk angka.
            if ($c === 1) return 52;
            $len = mb_strlen($longestLabelForCol[$c] ?? '');
            if ($len === 0) return 80;
            // ~6.4px per karakter (perkiraan lebar huruf kapital pada
            // font 12px bold) + padding kiri-kanan sel. Dibatasi supaya
            // kolom tetap compact - label panjang cukup turun ke 2-3
            // baris (bukan melebar jadi sangat panjang ke samping).
            $px = (int) round($len * 6.4 + 30);
            return max(80, min(190, $px));
        };

        // Tema warna header (biru-putih, mengikuti tema web) - dipetakan
        // berdasarkan BARIS header (1-4), menggantikan warna asli Excel
        // (oranye/peach/kuning) yang sebelumnya dipakai per kolom. Ini
        // hanya mengubah TAMPILAN warna; urutan, label, colspan/rowspan,
        // dan data sama sekali tidak diubah.
        $headerRowTheme = [
            1 => ['bg' => '#DCE6F2', 'color' => '#0B5EA8'], // baris nomor kolom
            2 => ['bg' => '#08466F', 'color' => '#FFFFFF'], // judul besar / grup utama
            3 => ['bg' => '#0B5EA8', 'color' => '#FFFFFF'], // sub-grup
            4 => ['bg' => '#3F7FBF', 'color' => '#FFFFFF'], // nama kolom
        ];
    @endphp

    @if(empty($dataRows) && empty($headerCells))
        <div class="plg2026-table-box">
            <div class="plg2026-empty-state">Tidak ada data untuk ditampilkan.</div>
        </div>
    @else
        <div class="plg2026-table-box">
            <table class="plg2026-table">
                <colgroup>
                    @for($c = 1; $c <= $totalCols; $c++)
                        <col style="width:{{ $colWidthPx($c) }}px;">
                    @endfor
                </colgroup>

                <thead>
                    @for($r = 1; $r <= 4; $r++)
                        <tr class="{{ $r === 1 ? 'plg2026-row-colnum' : '' }}">
                            @foreach($headerByRow[$r] as $hc)
                                @php
                                    $styleParts = [];
                                    // Warna header memakai tema biru-putih web (per baris),
                                    // bukan warna asli dari Excel.
                                    $theme = $headerRowTheme[$hc['r']] ?? ['bg' => '#0B5EA8', 'color' => '#FFFFFF'];
                                    $styleParts[] = 'background:'.$theme['bg'];
                                    $styleParts[] = 'color:'.$theme['color'];

                                    // Freeze kolom NO & NAMA PELANGGAN - hanya untuk sel
                                    // header yang murni 1 kolom (colspan=1); sel judul
                                    // gabungan yang melintasi kolom 1-2 tidak di-freeze.
                                    $freezeClass = '';
                                    if ($hc['cs'] === 1) {
                                        if ($hc['c'] === 1) {
                                            $freezeClass = 'plg2026-freeze-1';
                                        } elseif ($hc['c'] === 2) {
                                            $freezeClass = 'plg2026-freeze-2';
                                            $styleParts[] = 'left:'.$colWidthPx(1).'px';
                                        }
                                    }
                                @endphp
                                <th colspan="{{ $hc['cs'] }}" rowspan="{{ $hc['rs'] }}"
                                    class="{{ $freezeClass }}"
                                    style="{{ implode(';', $styleParts) }}">
                                    {!! $hc['label'] !== '' ? nl2br(e($hc['label'])) : '&nbsp;' !!}
                                </th>
                            @endforeach
                        </tr>
                    @endfor
                </thead>

                <tbody>
                    @forelse($dataRows as $r)
                        <tr data-row="{{ $r['number'] }}">
                            @for($c = 1; $c <= $totalCols; $c++)
                                @php
                                    $cell = $trim($r['cells'][$c - 1] ?? '');
                                    $isNumeric = $cell !== '' && is_numeric(str_replace(',', '', $cell));

                                    // PERBAIKAN: alignment isi tabel dibuat KONSISTEN -
                                    // SEMUA cell rata kiri (sebelumnya kolom NO rata
                                    // tengah, angka rata kanan, teks pendek <=6 karakter
                                    // rata tengah). vertical-align:middle sudah diatur
                                    // lewat CSS "plg2026-col-no" (kolom NO) di bawah.
                                    $align = ($c === 1) ? 'text-left plg2026-col-no' : 'text-left';

                                    // Freeze kolom NO & NAMA PELANGGAN di body.
                                    $freezeClass = $c === 1 ? 'plg2026-freeze-1' : ($c === 2 ? 'plg2026-freeze-2' : '');
                                    $freezeStyle = $c === 2 ? ' style="left:'.$colWidthPx(1).'px"' : '';
                                @endphp
                                <td class="{{ $align }} {{ $freezeClass }}"{!! $freezeStyle !!}>{{ $cell }}</td>
                            @endfor
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>

<script>
(function(){
    const btnBack = document.getElementById('plg2026BtnBackHistory');
    if (!btnBack) return;
    btnBack.addEventListener('click', function(e){
        if (window.history.length > 1 && document.referrer) {
            e.preventDefault();
            window.history.back();
        }
    });
})();

(function(){
    const params = new URLSearchParams(window.location.search);
    const highlightRow = params.get('highlight_row');
    if (!highlightRow) return;
    const target = document.querySelector('tr[data-row="' + highlightRow + '"]');
    if (!target) return;
    setTimeout(function(){
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        target.classList.add('plg2026-row-highlight');
        setTimeout(function(){ target.classList.remove('plg2026-row-highlight'); }, 4000);
    }, 200);
})();

// ================= TAMBAHAN: FREEZE/STICKY HEADER TABEL (dihitung otomatis) =================
// Header tabel ini punya 4 baris dengan sel rowspan & tinggi baris yang
// tidak seragam, jadi posisi "top" tiap baris TIDAK di-hardcode di CSS -
// dihitung di sini dari tinggi baris yang SUNGGUHAN dirender (offsetHeight),
// supaya baris header ke-2/3/4 selalu menempel tepat di bawah baris
// sebelumnya (tidak ada header ganda/tumpang tindih), presisi walau ukuran
// teks/kolom berubah. TIDAK mengubah data, hanya menambahkan gaya posisi.
(function(){
    function applyStickyHeaderRows(){
        const thead = document.querySelector('.plg2026-table thead');
        if (!thead) return;
        let top = 0;
        Array.prototype.forEach.call(thead.rows, function(row){
            Array.prototype.forEach.call(row.cells, function(cell){
                cell.style.top = top + 'px';
            });
            top += row.offsetHeight;
        });
    }
    applyStickyHeaderRows();
    window.addEventListener('load', applyStickyHeaderRows);
    window.addEventListener('resize', applyStickyHeaderRows);
})();
</script>

</body>
</html>