<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    @vite('resources/css/login.css')
</head>
<body>

<div class="container">

    <div class="login-container">

        <!-- BAGIAN KIRI -->
        <div class="left-panel">

            <img src="{{ asset('images/login.jpg') }}" class="bg-image" alt="PLN">

            <div class="overlay"></div>

            <div class="left-content">

                <div class="brand-badge">
                    <img src="{{ asset('images/logo-siginjai.png') }}" alt="Logo Tugu Keris Siginjai">
                </div>

                <span class="brand-eyebrow">ADMIN PANEL</span>

                <div class="brand-logo">SIGINJAI</div>

                <p class="brand-tagline">
                    Sistem Gerbang Informasi Jasa , Anggaran & Investasi
                </p>

                <p class="brand-org">PLN UP3 Jambi</p>

            </div>

        </div>

        <!-- BAGIAN KANAN -->
        <div class="right-panel">

            <div class="login-box">

                <h1>Admin</h1>

                @if ($errors->any())
                    <div class="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.login') }}" method="POST">

                    @csrf

                    <label>Email Admin :</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Masukkan Email Admin"
                        required
                    >

                    <label>Password :</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan Password"
                        required
                    >

                    <button type="submit" class="btn-login">
                        Log in
                    </button>

                </form>

                <a href="{{ route('login.view') }}" class="btn-admin">
                    ← Kembali ke Login User
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>