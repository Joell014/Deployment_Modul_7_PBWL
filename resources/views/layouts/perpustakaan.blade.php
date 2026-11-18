<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Perpustakaan')</title>

    {{-- CSS tema perpustakaan dipisah dari app.css milik Breeze (Tailwind) --}}
    @vite(['resources/css/perpustakaan.css'])
</head>

<body>

    <header class="header">
        <div class="container">
            <h1>Perpustakaan Digital</h1>
            <p>Sistem Informasi Koleksi Buku</p>
        </div>
    </header>

    <nav class="navbar">
        <div class="container nav-content">

            <a href="{{ route('home') }}">
                Beranda
            </a>

            <a href="{{ route('buku.index') }}">
                Daftar Buku
            </a>

            {{-- Menu akun hanya tampil kalau Breeze sudah terpasang (route 'login' ada) --}}
            @if (Route::has('login'))
            <div class="nav-akun">

                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>

                    <a href="{{ route('profile.edit') }}">{{ Auth::user()->name }}</a>

                    {{-- Logout harus POST, jadi memakai form + @csrf --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-keluar">Keluar</button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                @endguest

            </div>
            @endif

        </div>
    </nav>

    <main class="container content">

        @yield('content')

    </main>

    <footer class="footer">
        <div class="container">
            <p>
                &copy; 2026 Perpustakaan Digital
            </p>
        </div>
    </footer>

</body>

</html>