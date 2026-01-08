<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proman</title>

    <linkpreconnect="https: //fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#FAFAFA] text-gray-800 font-sans">

    <nav id="navbar"
        class="fixed top-0 left-0 w-full bg-[#FAFAFA] py-4 transition-all duration-300 z-50">

        <div class="flex items-center justify-between px-6 md:px-12">

            <!-- Logo (pojok kiri) -->
            <div class="font-bold text-2xl flex items-center gap-2">
                <a href="/">
                    <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white">
                        P
                    </div>
                </a>
            </div>

            <!-- Menu -->
            <div class="flex gap-4">
                @if (Route::has('login'))
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="font-semibold hover:text-blue-600 transition">
                    Dashboard
                </a>
                @else
                <a href="{{ route('login') }}"
                    class="font-medium hover:text-blue-600 transition py-2">
                    Log in
                </a>

                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="bg-blue-600 text-white px-5 py-2 rounded-full font-medium hover:bg-gray-700 transition hidden md:block">
                    Mulai Sekarang
                </a>
                @endif
                @endauth
                @endif
            </div>

        </div>
    </nav>

    <main class="mt-16 md:mt-24 px-6 text-center max-w-4xl mx-auto">
        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
            Versi Beta 1.0
        </span>

        <h1 class="text-4xl md:text-6xl font-bold mt-6 leading-tight tracking-tight text-gray-900">
            Kelola Proyekmu Menggunakan Proman <br> <span class="text-blue-600">Tanpa Ribet.</span>
        </h1>

        <p class="text-gray-500 text-lg md:text-xl mt-6 max-w-2xl mx-auto leading-relaxed">
            Aplikasi sederhana untuk mencatat, melacak, dan menyelesaikan target belajarmu.
            Dibuat dengan Laravel 12 & Livewire.
        </p>

        <div class="mt-10 flex flex-col md:flex-row justify-center gap-4">
            @auth
            <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">
                Buka Dashboard
            </a>
            @else
            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">
                Buat Akun Gratis
            </a>
            <a href="{{ route('login') }}" class="bg-white text-gray-700 border border-gray-200 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition">
                Masuk
            </a>
            @endauth
        </div>

        <div class="mt-16 p-2 bg-white rounded-2xl shadow-xl border border-gray-100 rotate-1 hover:rotate-0 transition duration-500">
            <div class="bg-gray-50 rounded-xl h-64 md:h-96 w-full flex items-center justify-center text-gray-300">
                <span class="text-4xl font-bold opacity-20">Preview Dashboard Disini</span>
            </div>
        </div>
    </main>

    <section class="py-24 px-6 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-6">
                    ⚡
                </div>
                <h3 class="text-xl font-bold mb-2">Cepat & Ringan</h3>
                <p class="text-gray-500">Dibangun dengan teknologi terbaru Laravel Volt yang super kencang.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mb-6">
                    🔒
                </div>
                <h3 class="text-xl font-bold mb-2">Aman Terkendali</h3>
                <p class="text-gray-500">Sistem login dan data kamu diproteksi dengan keamanan standar industri.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mb-6">
                    📱
                </div>
                <h3 class="text-xl font-bold mb-2">Mobile Friendly</h3>
                <p class="text-gray-500">Akses daftar tugasmu dari laptop, tablet, atau HP di mana saja.</p>
            </div>
        </div>
    </section>

    <footer class="text-center py-10 text-gray-400 text-sm">
        &copy; {{ date('Y') }} Project Belajar Laravel. Dibuat dengan Scratching My Balls Inc.
    </footer>

    <script>
        const navbar = document.getElementById('navbar');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                navbar.classList.add('border-b', 'border-black');
            } else {
                navbar.classList.remove('border-b', 'border-black');
            }
        });
    </script>


</body>

</html>