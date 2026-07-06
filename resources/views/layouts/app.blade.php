<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring PLN</title>
    @vite('resources/css/dashboard.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <h2>PLN</h2>
        </div>
        <ul class="nav-menu">
            <li onclick="window.location.href='{{ url('/dashboard') }}'">
                <span class="material-icons">dashboard</span><span>Dashboard</span>
            </li>
            <li onclick="window.location.href='{{ url('/login') }}'">
                <span class="material-icons">logout</span><span>Logout</span>
            </li>
        </ul>
    </aside>

    <main class="content">
        @yield('konten_halaman')
    </main>
</div>
</body>
</html>