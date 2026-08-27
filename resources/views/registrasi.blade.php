<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi User</title>

    @vite('resources/css/registrasi.css')
</head>
<body>

<div class="container">

    <div class="registrasi-container">

        <!-- ========================= -->
        <!-- BAGIAN KIRI -->
        <!-- ========================= -->

        <div class="left-panel">

            <img src="{{ asset('images/login.jpg') }}" class="bg-image" alt="PLN">

            <div class="overlay"></div>

            <div class="left-content">

                <div class="brand-badge">
                    <img src="{{ asset('images/logo-siginjai.png') }}" alt="Logo Tugu Keris Siginjai">
                </div>

                <div class="brand-logo">SIGINJAI</div>

                <p class="brand-tagline">
                    Sistem Gerbang Informasi Jasa , Anggaran & Investasi
                </p>

                <p class="brand-org">PLN UP3 Jambi</p>

            </div>

        </div>

        <!-- ========================= -->
        <!-- BAGIAN KANAN -->
        <!-- ========================= -->

        <div class="right-panel">

            <div class="registrasi-box">

                <h1>Register</h1>

                {{-- Pesan sukses --}}
                @if(session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Error validasi --}}
                @if ($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.store') }}" method="POST">

                    @csrf

                    <label for="name">Nama Lengkap :</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >

                    <label for="email">E-mail :</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >

                    <label for="password">Password :</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >

                    <button type="submit">
                        Register
                    </button>

                </form>

                <p class="login-link">
                    Sudah punya akun?
                    <a href="{{ url('/') }}">Login</a>
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>