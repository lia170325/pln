<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    @vite('resources/css/dashboard.css')
    </head>
<body>
    <div class="wrapper">
        <aside class="sidebar">...</aside>

        <main class="content">
            <a href="{{ url('/dashboard') }}" class="btn-back">Kembali</a>
            <h2>📋 {{ $judul }}</h2>

            <div class="card">
                <div class="table-container">
                    <table class="excel-table">
                        {!! $tabel_html !!} 
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>