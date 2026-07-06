<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | PLN UP3 Jambi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 bg-dark text-white min-vh-100 p-3">
                <h4>PLN UP3 Jambi</h4>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link text-white" href="/dashboard-admin">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/input-data">Input Data</a></li>
                    <li class="nav-item mt-4">
                        <a class="nav-link text-danger" href="{{ route('logout.admin') }}">Logout</a>
                    </li>
                </ul>
            </nav>
            <main class="col-md-10 p-4">
                <h2>Selamat Datang, {{ session('nama') }}</h2>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card p-4 shadow-sm border-0">
                            <h5>Monitoring KHS</h5>
                            <p>Panel Monitoring KHS PLN UP3 Jambi</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>