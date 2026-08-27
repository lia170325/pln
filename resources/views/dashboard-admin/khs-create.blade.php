<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data KHS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Memanggil file CSS khusus KHS melalui Vite -->
    @vite('resources/css/admin/khs.css')
</head>
<body class="d-flex align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="form-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Data KHS Baru</h5>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary text-white">Kembali</a>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('admin.khs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label>Nama Proyek / KHS</label>
                                <input type="text" name="nama_proyek" class="form-control" placeholder="Masukkan nama proyek..." required>
                            </div>
                            <div class="mb-3">
                                <label>Link ArcGIS (Opsional)</label>
                                <input type="url" name="arcgis_link" class="form-control" placeholder="https://...">
                            </div>
                            <div class="mb-3">
                                <label>Upload Gambar (Opsional saat ini)</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-4">
                                <label>Upload Kontrak PDF (Opsional saat ini)</label>
                                <input type="file" name="kontrak_pdf" class="form-control" accept="application/pdf">
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>