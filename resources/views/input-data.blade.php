<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data</title>

    @vite('resources/css/input-data.css')

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<div class="wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="logo">
            <img src="{{ asset('images/logo-pln.png') }}" alt="PLN">
        </div>

        <h4>MENU ADMIN</h4>

        <ul>
            <li class="active">
                <span class="material-icons">add_circle_outline</span>
                Input Data
            </li>

            <li>
                <span class="material-icons">check_circle_outline</span>
                Update Data
            </li>
        </ul>

    </aside>

    <!-- Content -->
    <main class="content">

        <div class="card">

            <div class="header-title">

                <div>
                    <h2>Form Input Data</h2>
                    <small>Lengkapi informasi sheet yang akan ditambahkan</small>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn-back">
                    <span class="material-icons">arrow_back</span>
                    Kembali
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

            <form action="{{ route('input-data.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="form-row">

                    <div class="form-group">
                        <label>Jenis Data *</label>

                        <select name="jenis_data" id="jenis_data" required>

                            <option value="">-- Pilih Jenis Data --</option>

                            <option value="KHS JASA">KHS JASA</option>

                            <option value="KHS PEMBERSIHAN">KHS PEMBERSIHAN</option>

                            <option value="REGRESASI">REGRESASI</option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Tahun *</label>

                        <select name="tahun" id="tahun" required>

                            <option value="">-- Pilih Tahun --</option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>ID Spreadsheet *</label>

                        <input
                            type="text"
                            name="id_spreadsheet"
                            placeholder="Masukkan ID Spreadsheet"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>Jumlah Sheet *</label>

                        <input
                            type="number"
                            name="jumlah_sheet"
                            placeholder="Contoh : 12"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>Total Baris Data *</label>

                        <input
                            type="number"
                            name="total_baris"
                            placeholder="Contoh : 15000"
                            required
                        >

                    </div>

                </div>

                <input type="hidden" name="nama_sheet" id="nama_sheet">

                <label class="upload-title">

                    Upload File Excel *

                </label>

                <label class="upload-box">

                    <span class="material-icons">

                        upload_file

                    </span>

                    <p id="file-name">

                        Klik untuk memilih file Excel

                    </p>

                    <input
                        type="file"
                        id="file_excel"
                        name="file_excel"
                        accept=".xls,.xlsx"
                        required
                    >

                </label>

                <div class="button-group">

                    <button type="reset" class="btn-cancel">

                        Batal

                    </button>

                    <button type="submit" class="btn-save">

                        Simpan Sheet

                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

<script>

const jenis = document.getElementById("jenis_data");
const tahun = document.getElementById("tahun");
const namaSheet = document.getElementById("nama_sheet");

jenis.addEventListener("change", function(){

    tahun.innerHTML = '<option value="">-- Pilih Tahun --</option>';

    if(this.value === "KHS JASA" || this.value === "KHS PEMBERSIHAN"){

        tahun.innerHTML += '<option value="2024">2024</option>';
        tahun.innerHTML += '<option value="2025">2025</option>';
        tahun.innerHTML += '<option value="2026">2026</option>';

    }

    if(this.value === "REGRESASI"){

        tahun.innerHTML += '<option value="2025">2025</option>';
        tahun.innerHTML += '<option value="2026">2026</option>';

    }

});

tahun.addEventListener("change", function(){

    if(jenis.value !== "" && this.value !== ""){

        namaSheet.value = jenis.value + " " + this.value;

    }

});

const file = document.getElementById("file_excel");

file.addEventListener("change", function(){

    if(this.files.length > 0){

        document.getElementById("file-name").innerHTML = this.files[0].name;

    }

});

</script>

</body>
</html>