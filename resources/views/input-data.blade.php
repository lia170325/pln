<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Monitoring Excel</title>

    @vite('resources/css/input-data.css')

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* TAMBAHAN: styling untuk 3 form upload terpisah */
        .upload-section-title{
            margin: 30px 0 12px 0;
            font-size: 15px;
            font-weight: bold;
            color: #cbd5e1;
        }
        .upload-section-title:first-of-type{
            margin-top: 0;
        }
        .upload-section-desc{
            font-size: 12px;
            color: #64748b;
            margin-bottom: 14px;
        }
        .upload-divider{
            border: none;
            border-top: 1px solid rgba(255,255,255,.08);
            margin: 28px 0;
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="logo">
            <img src="data:image/png;base64,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" alt="Logo PLN" class="sidebar-logo-pln">
        </div>

        <h4>MENU ADMIN</h4>

        <ul>
            <li class="active">
                <span class="material-icons">upload_file</span>
                Upload Monitoring
            </li>
        </ul>

    </aside>

    <!-- Content -->
    <main class="content">

        <div class="card">

            <div class="header-title">

                <div>
                    <h2>Upload Monitoring Excel</h2>
                    <small>Upload file Excel sesuai jenis monitoring (KHS, Tiang, atau Pelanggan)</small>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn-back">
                    <span class="material-icons">arrow_back</span>
                    Dashboard
                </a>

            </div>

            <hr>

            @if(session('success'))
                <div class="success-box">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="error-box">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ================= 1. UPLOAD MONITORING KHS ================= -->

            <p class="upload-section-title">1. Upload Monitoring KHS</p>
            <p class="upload-section-desc">
                File Excel Monitoring KHS (KHS Jasa, KHS Cover, Reservasi, Rekap, dsb).
            </p>

            <form action="{{ route('input-data.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="js-upload-form"
                  data-jenis="Monitoring KHS">

                @csrf

                <label class="upload-box">
                    <span class="material-icons">upload_file</span>
                    <p class="js-file-name">Klik untuk memilih file Excel (.xlsx) Monitoring KHS</p>
                    <input type="file" class="js-file-input" name="file_excel" accept=".xls,.xlsx" required>
                </label>

                <div class="upload-progress-wrap js-upload-progress-wrap" style="display:none;">
                    <div class="upload-progress-track">
                        <div class="upload-progress-fill js-upload-progress-fill" style="width:0%;"></div>
                    </div>
                    <div class="upload-progress-info">
                        <span class="upload-progress-status js-upload-progress-status">Menyiapkan file...</span>
                        <span class="upload-progress-percent js-upload-progress-percent">0%</span>
                    </div>
                    <div class="js-upload-progress-result"></div>
                </div>

                <div class="button-group">
                    <button type="reset" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-save">Upload &amp; Import KHS</button>
                </div>

                <div class="upload-cancel-notice js-upload-cancel-notice" style="display:none;"></div>

            </form>

            <hr class="upload-divider">

            <!-- ================= 2. UPLOAD MONITORING TIANG ================= -->

            <p class="upload-section-title">2. Upload Monitoring Tiang</p>
            <p class="upload-section-desc">
                File Excel Monitoring Tiang (Total Vendor, Rekap KR, RPB per SPB, TA per SPB, Maxima per SPB, WIKA per SPB, dsb).
            </p>

            <form action="{{ route('input-data.store.tiang') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="js-upload-form"
                  data-jenis="Monitoring Tiang">

                @csrf

                <label class="upload-box">
                    <span class="material-icons">upload_file</span>
                    <p class="js-file-name">Klik untuk memilih file Excel (.xlsx) Monitoring Tiang</p>
                    <input type="file" class="js-file-input" name="file_excel" accept=".xls,.xlsx" required>
                </label>

                <div class="upload-progress-wrap js-upload-progress-wrap" style="display:none;">
                    <div class="upload-progress-track">
                        <div class="upload-progress-fill js-upload-progress-fill" style="width:0%;"></div>
                    </div>
                    <div class="upload-progress-info">
                        <span class="upload-progress-status js-upload-progress-status">Menyiapkan file...</span>
                        <span class="upload-progress-percent js-upload-progress-percent">0%</span>
                    </div>
                    <div class="js-upload-progress-result"></div>
                </div>

                <div class="button-group">
                    <button type="reset" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-save">Upload &amp; Import Tiang</button>
                </div>

                <div class="upload-cancel-notice js-upload-cancel-notice" style="display:none;"></div>

            </form>

            <hr class="upload-divider">

            <!-- ================= 3. UPLOAD MONITORING PELANGGAN ================= -->

            <p class="upload-section-title">3. Upload Monitoring Pelanggan</p>
            <p class="upload-section-desc">
                File Excel Monitoring Pelanggan (Pelanggan 2026, dsb).
            </p>

            <form action="{{ route('input-data.store.pelanggan') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="js-upload-form"
                  data-jenis="Monitoring Pelanggan">

                @csrf

                <label class="upload-box">
                    <span class="material-icons">upload_file</span>
                    <p class="js-file-name">Klik untuk memilih file Excel (.xlsx) Monitoring Pelanggan</p>
                    <input type="file" class="js-file-input" name="file_excel" accept=".xls,.xlsx" required>
                </label>

                <div class="upload-progress-wrap js-upload-progress-wrap" style="display:none;">
                    <div class="upload-progress-track">
                        <div class="upload-progress-fill js-upload-progress-fill" style="width:0%;"></div>
                    </div>
                    <div class="upload-progress-info">
                        <span class="upload-progress-status js-upload-progress-status">Menyiapkan file...</span>
                        <span class="upload-progress-percent js-upload-progress-percent">0%</span>
                    </div>
                    <div class="js-upload-progress-result"></div>
                </div>

                <div class="button-group">
                    <button type="reset" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-save">Upload &amp; Import Pelanggan</button>
                </div>

                <div class="upload-cancel-notice js-upload-cancel-notice" style="display:none;"></div>

            </form>

        </div>

    </main>

</div>

<script>

// TAMBAHAN: berlaku untuk ketiga form upload (KHS/Tiang/Pelanggan)
document.querySelectorAll('.js-file-input').forEach(function (input) {

    input.addEventListener('change', function () {

        const label = this.closest('.upload-box').querySelector('.js-file-name');

        if (this.files.length > 0) {
            label.innerHTML = this.files[0].name;
        }

    });

});

// ==========================
// TAMBAHAN: PROGRESS BAR UPLOAD & IMPORT EXCEL
// ==========================
// Berlaku untuk ketiga form upload (KHS/Tiang/Pelanggan) secara independen
// (masing-masing form punya widget progress-nya sendiri lewat partial
// resources/views/partials/upload-progress.blade.php, jadi progress satu
// upload TIDAK bercampur dengan upload lain).
//
// Cara kerja: form yang tadinya submit biasa (reload halaman) di-intercept
// dan dikirim lewat XMLHttpRequest supaya progress upload BYTE-nya bisa
// dibaca secara nyata dari event xhr.upload.progress (bukan animasi
// palsu). Tahap "membaca/mengimpor Excel" di backend TIDAK bisa diberi
// persentase pasti (single request, tidak ada mekanisme progress
// tambahan) - jujur ditampilkan sebagai status teks + bar berdenyut
// (bukan angka yang dipalsukan naik ke 100%).
//
// Server tetap membalas seperti SEBELUMNYA (redirect + session flash)
// untuk form submit biasa; balasan JSON di sini HANYA dipakai kalau
// request memang mengirim header "Accept: application/json" (lihat
// InputDataController::importExcel -> $request->expectsJson()), jadi
// endpoint lama tetap 100% kompatibel untuk pemanggil lain.
document.querySelectorAll('.js-upload-form').forEach(function (form) {

    const wrap    = form.querySelector('.js-upload-progress-wrap');
    const fill    = form.querySelector('.js-upload-progress-fill');
    const status  = form.querySelector('.js-upload-progress-status');
    const percent = form.querySelector('.js-upload-progress-percent');
    const result  = form.querySelector('.js-upload-progress-result');
    const fileInp = form.querySelector('.js-file-input');
    const btnSave = form.querySelector('.btn-save');
    const fileLabel = form.querySelector('.js-file-name');
    const cancelNotice = form.querySelector('.js-upload-cancel-notice');

    if (!wrap || !fill || !status || !percent || !result) return;

    // TAMBAHAN: teks placeholder asli label pilih-file, direkam SEKALI di
    // sini (sebelum ada file dipilih) supaya tombol "Batal" bisa
    // mengembalikan tulisannya persis seperti kondisi awal - form reset
    // bawaan browser TIDAK menyentuh teks ini karena bukan value input,
    // melainkan teks biasa yang diubah lewat JS saat file dipilih.
    const labelDefaultText = fileLabel ? fileLabel.textContent : '';

    // TAMBAHAN: status permintaan upload saat ini untuk form ini -
    // 'idle' (belum ada upload berjalan), 'uploading' (file masih
    // dikirim ke server, AMAN dibatalkan karena backend belum mulai
    // membaca/impor Excel), atau 'processing' (seluruh file sudah
    // diterima server dan sedang dibaca/diimpor - membatalkan dari
    // browser TIDAK menjamin proses di server ikut berhenti). xhrAktif
    // menyimpan request yang sedang berjalan supaya tombol Batal bisa
    // membatalkannya (xhr.abort()).
    let phase = 'idle';
    let xhrAktif = null;

    // TAMBAHAN: percent yang SEDANG ditampilkan di layar (state lokal per
    // form). Dipakai supaya angka terlihat berjalan naik 1%, 2%, 3%, ...
    // menuju persen NYATA yang dilaporkan xhr.upload.progress, alih-alih
    // langsung "meloncat" ke angka itu (yang terjadi kalau file kecil
    // sehingga event progress cuma sedikit/jarang muncul). Titik AKHIR
    // animasi selalu angka yang benar-benar sudah diketahui (byte yang
    // sudah terkirim / tahap yang sudah tercapai) - yang dihaluskan
    // hanya CARA menampilkannya, bukan angkanya sendiri.
    let displayedPct = 0;
    let animId = null;

    function setProgress(pct, teks) {
        fill.style.width = pct + '%';
        percent.textContent = pct + '%';
        if (teks) status.textContent = teks;
    }

    function animateProgressTo(targetPct, teks) {
        if (animId) cancelAnimationFrame(animId);

        const mulaiPct = displayedPct;
        const selisih  = targetPct - mulaiPct;

        if (selisih <= 0) {
            displayedPct = targetPct;
            setProgress(targetPct, teks);
            return;
        }

        const waktuMulai = performance.now();
        // Durasi mengikuti besar selisihnya (naik jauh = sedikit lebih
        // lama), dibatasi supaya tetap terasa responsif.
        const durasi = Math.min(1000, Math.max(300, selisih * 15));

        function langkah(sekarang) {
            const t = Math.min(1, (sekarang - waktuMulai) / durasi);
            const nilaiSekarang = Math.round(mulaiPct + selisih * t);
            if (nilaiSekarang !== displayedPct) {
                displayedPct = nilaiSekarang;
                setProgress(nilaiSekarang, teks);
            } else if (teks) {
                status.textContent = teks;
            }
            if (t < 1) {
                animId = requestAnimationFrame(langkah);
            } else {
                animId = null;
            }
        }

        animId = requestAnimationFrame(langkah);
    }

    form.addEventListener('submit', function (e) {

        // Kalau browser belum sempat mem-fetch (mis. konfigurasi lama),
        // biarkan submit biasa berjalan supaya upload tetap bisa
        // dilakukan (fallback aman, bukan silent fail).
        if (typeof window.FormData === 'undefined' || typeof window.XMLHttpRequest === 'undefined') {
            return;
        }

        e.preventDefault();

        result.innerHTML = '';
        wrap.style.display = '';
        fill.classList.remove('is-processing', 'is-error');
        if (btnSave) btnSave.disabled = true;
        if (cancelNotice) { cancelNotice.style.display = 'none'; cancelNotice.innerHTML = ''; }

        if (animId) cancelAnimationFrame(animId);
        displayedPct = 0;
        setProgress(0, 'Menyiapkan file...');

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhrAktif = xhr;
        phase = 'uploading';

        xhr.open('POST', form.getAttribute('action'), true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        // Progress upload NYATA (bukan animasi kosong) - dibatasi maksimal
        // 90% supaya sisa 10% mewakili tahap baca/import Excel di server
        // yang baru selesai saat respons diterima (xhr.onload). Angka
        // TARGET-nya asli dari byte yang sudah terkirim; animateProgressTo
        // hanya menghaluskan tampilan supaya kelihatan berjalan
        // 1%, 2%, 3%, ... naik ke angka itu (bukan meloncat langsung).
        xhr.upload.addEventListener('progress', function (ev) {
            if (!ev.lengthComputable) return;
            const pctUpload = Math.round((ev.loaded / ev.total) * 90);
            animateProgressTo(pctUpload, 'Sedang mengupload file...');
        });

        xhr.upload.addEventListener('load', function () {
            phase = 'processing';
            animateProgressTo(90, 'Sedang membaca & mengimpor data Excel...');
            fill.classList.add('is-processing');
        });

        function tampilkanGagal(pesan) {
            if (animId) cancelAnimationFrame(animId);
            fill.classList.remove('is-processing');
            fill.classList.add('is-error');
            status.textContent = '❌ Upload/import gagal.';
            result.innerHTML = '<div class="error-box">' + pesan + '</div>';
            if (btnSave) btnSave.disabled = false;
            phase = 'idle';
            xhrAktif = null;
        }

        xhr.onload = function () {

            // TAMBAHAN: kalau xhr ini sudah dibatalkan lewat tombol Batal
            // (xhrAktif sudah diganti/null), abaikan - UI sudah di-reset
            // duluan oleh handler 'reset', jangan ditimpa lagi di sini.
            if (xhrAktif !== xhr) return;

            fill.classList.remove('is-processing');

            let data = null;
            try { data = JSON.parse(xhr.responseText); } catch (err) { data = null; }

            if (xhr.status >= 200 && xhr.status < 300 && data && data.success) {
                animateProgressTo(100, 'Upload dan import selesai.');
                result.innerHTML = '<div class="success-box">✅ ' + data.message + '</div>';
                if (btnSave) btnSave.disabled = false;
                if (fileInp) fileInp.value = '';
                phase = 'idle';
                xhrAktif = null;
                return;
            }

            // Gagal: kumpulkan pesan error dari respons validasi Laravel
            // (format { message, errors: {field:[...]} }) atau dari JSON
            // kustom { success:false, message }.
            let pesan = 'Terjadi kesalahan saat upload/import.';
            if (data) {
                if (data.message) pesan = data.message;
                if (data.errors) {
                    const semuaPesan = Object.values(data.errors).flat();
                    if (semuaPesan.length) pesan = semuaPesan.join(' ');
                }
            }
            tampilkanGagal(pesan);
        };

        xhr.onerror = function () {
            if (xhrAktif !== xhr) return;
            fill.classList.remove('is-processing');
            tampilkanGagal('Tidak dapat terhubung ke server. Periksa koneksi lalu coba lagi.');
        };

        xhr.send(formData);
    });

    // ==========================
    // TAMBAHAN: TOMBOL "BATAL" - BENAR-BENAR MEMBATALKAN FILE/UPLOAD
    // ==========================
    // Tombol Batal sudah type="reset" (mengosongkan input file secara
    // native), tapi event 'reset' bawaan TIDAK menyentuh state lain yang
    // diatur lewat JS (teks label nama file, progress bar, request yang
    // sedang berjalan). Listener ini menyambung ke event 'reset' yang
    // sama supaya SELURUH state ikut kembali ke kondisi awal dalam satu
    // aksi klik, sesuai tombol yang sudah ada (tidak menambah tombol
    // baru / tidak mengubah tipe tombolnya).
    form.addEventListener('reset', function () {

        // Kalau masih ada request yang jalan (upload maupun processing),
        // batalkan dari sisi browser kalau masih bisa (xhr.abort()).
        // Baik masih 'uploading' maupun sudah 'processing' di server,
        // tombol Batal SELALU melakukan silent reset di UI - tidak ada
        // pesan/alert/warning apa pun yang ditampilkan ke user.
        if (xhrAktif) {
            try { xhrAktif.abort(); } catch (err) { /* abaikan */ }
            xhrAktif = null;
        }
        phase = 'idle';

        if (animId) cancelAnimationFrame(animId);
        displayedPct = 0;

        wrap.style.display = 'none';
        fill.classList.remove('is-processing', 'is-error');
        fill.style.width = '0%';
        percent.textContent = '0%';
        status.textContent = 'Menyiapkan file...';
        result.innerHTML = '';

        if (fileLabel) fileLabel.textContent = labelDefaultText;
        if (btnSave) btnSave.disabled = false;

        if (cancelNotice) {
            cancelNotice.style.display = 'none';
            cancelNotice.innerHTML = '';
        }
    });

});

</script>

</body>
</html>