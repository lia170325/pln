<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit / Upload KHS | Admin</title>
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
                        <h5 class="mb-0">Edit / Upload Data KHS</h5>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary text-white">Batal</a>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('admin.khs.update', $khs->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label>Nama Proyek / KHS</label>
                                <input type="text" name="nama_proyek" class="form-control" value="{{ $khs->nama_proyek }}" required>
                            </div>
                            <div class="mb-4">
                                <label>Link ArcGIS</label>
                                <input type="url" name="arcgis_link" class="form-control" value="{{ $khs->arcgis_link }}" placeholder="https://www.arcgis.com/...">
                            </div>
                            
                            <div class="mb-4">
                                <label>Upload Gambar Baru</label>
                                @if($khs->gambar)
                                    <div class="mb-3 text-center p-3 border rounded bg-light">
                                        <img src="{{ asset('storage/' . $khs->gambar) }}" class="img-thumbnail rounded" width="150" alt="Preview Gambar">
                                        <div class="mt-2"><small class="text-success fw-bold">Gambar saat ini</small></div>
                                    </div>
                                @endif
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                <small class="text-muted mt-1 d-block">*Biarkan kosong jika tidak ingin mengubah gambar.</small>
                            </div>
                            
                            <div class="mb-5">
                                <label>Upload Kontrak PDF Baru</label>
                                @if($khs->kontrak_pdf)
                                    <div class="mb-3 p-3 border rounded bg-light text-center">
                                        <a href="{{ asset('storage/' . $khs->kontrak_pdf) }}" target="_blank" class="btn btn-sm btn-info text-white px-3 py-2">
                                            Buka PDF Saat Ini
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="kontrak_pdf" class="form-control" accept="application/pdf">
                                <small class="text-muted mt-1 d-block">*Biarkan kosong jika tidak ingin mengubah file PDF.</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>