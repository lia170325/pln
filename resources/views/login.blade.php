<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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

                <h2>
                    WEBSITE MONITORING <br>
                    KHS PLN UP3 JAMBI
                </h2>

            </div>

        </div>

        <!-- BAGIAN KANAN -->
        <div class="right-panel">

            <div class="login-box">

                <h1>Log In</h1>

                @if ($errors->any())
                    <div class="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">

                    @csrf

                    <label>Email :</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan Email"
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
                        Login User
                    </button>

                </form>

                <!-- Tombol Login Admin -->
                <a href="{{ route('login.admin') }}" class="btn-admin">
                    Login Admin
                </a>

                <p class="registrasi">
                    Belum punya akun ?
                    <a href="{{ url('/registrasi') }}">
                        Registrasi
                    </a>
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>