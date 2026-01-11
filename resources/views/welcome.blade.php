<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proman — Project Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="antialiased bg-[#FAFAFA] text-slate-800 font-sans selection:bg-blue-100">

    <nav id="navbar" class="fixed top-0 left-0 w-full py-4 transition-all duration-300 z-50">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-8">
            <div class="font-bold text-2xl flex items-center gap-2 group">
                <a href="/">
                    <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-lg transition-transform group-hover:scale-105">
                        P
                    </div>
                </a>
                <span class="font-bold text-lg tracking-tight hidden sm:block">Proman</span>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                @auth
                <a href="{{ url('/dashboard') }}" class="text-xs font-bold bg-neutral-900 text-white px-5 py-2 rounded-lg hover:bg-neutral-800 transition">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition py-2">Log in</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="hidden md:block bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-xs hover:bg-blue-700 transition">
                    Mulai Sekarang
                </a>
                @endif
                @endauth
                @endif
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 px-6 text-center max-w-4xl mx-auto">
        <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
            Versi Beta 1.0
        </span>

        <h1 class="text-4xl md:text-5xl font-extrabold mt-6 leading-tight tracking-tight text-gray-900">
            Kelola Proyekmu Menggunakan Proman <br>
            <span class="text-blue-600">Tanpa Batas.</span>
        </h1>

        <p class="text-gray-500 text-base md:text-lg mt-6 max-w-xl mx-auto leading-relaxed font-medium">
            Aplikasi sederhana untuk mencatat, melacak, dan menyelesaikan target belajarmu.
            Dibuat dengan Laravel 12 & Livewire.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            @auth
            <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">
                Buka Dashboard
            </a>
            @else
            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">
                Buat Akun Gratis
            </a>
            <a href="{{ route('login') }}" class="bg-white text-gray-700 border border-gray-200 px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-gray-50 transition">
                Masuk
            </a>
            @endauth
        </div>

        <div x-data="{ theme: 'light', activeSlide: 1 }" class="mt-16">

            <div class="flex justify-center mb-10">
                <div class="inline-flex p-1 bg-gray-100 rounded-2xl border border-gray-200">
                    <button @click="theme = 'light'"
                        :class="theme === 'light' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-400'"
                        class="px-6 py-2 rounded-xl text-xs font-black transition-all">
                        LIGHT MODE
                    </button>
                    <button @click="theme = 'dark'"
                        :class="theme === 'dark' ? 'bg-neutral-900 shadow-sm text-blue-400' : 'text-gray-400'"
                        class="px-6 py-2 rounded-xl text-xs font-black transition-all">
                        DARK MODE
                    </button>
                </div>
            </div>

            <div class="relative group">
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[3rem] blur-2xl opacity-10 group-hover:opacity-20 transition duration-1000"></div>

                <div class="relative bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden transform rotate-1 group-hover:rotate-0 transition duration-700">
                    <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        </div>
                        <div class="mx-auto bg-white border border-gray-200 rounded-lg px-4 py-1 text-[10px] text-gray-400 font-medium">
                            proman-app.test/dashboard
                        </div>
                    </div>

                    <div class="relative aspect-video overflow-hidden bg-gray-100">
                        @for ($i = 1; $i <= 10; $i++)
                            <img x-show="theme === 'light' && activeSlide === {{ $i }}"
                            src="{{ asset('image/dashboard/light-' . $i . '.png') }}"
                            class="w-full h-full object-cover shadow-inner"
                            alt="Dashboard Light {{ $i }}">

                            <img x-show="theme === 'dark' && activeSlide === {{ $i }}"
                                src="{{ asset('image/dashboard/dark-' . $i . '.png') }}"
                                class="w-full h-full object-cover shadow-inner"
                                alt="Dashboard Dark {{ $i }}">
                            @endfor

                            <button @click="activeSlide = activeSlide > 1 ? activeSlide - 1 : 10"
                                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur p-2 rounded-full shadow-lg z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button @click="activeSlide = activeSlide < 10 ? activeSlide + 1 : 1"
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur p-2 rounded-full shadow-lg z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                    </div>
                </div>

                <div class="flex justify-center gap-2 mt-8">
                    @for ($i = 1; $i <= 10; $i++)
                        <button @click="activeSlide = {{ $i }}"
                        :class="activeSlide === {{ $i }} ? 'w-8 bg-blue-600' : 'w-2 bg-gray-200'"
                        class="h-2 rounded-full transition-all duration-300">
                        </button>
                        @endfor
                </div>
            </div>
        </div>
    </main>

    <section class="py-20 px-8 max-w-6xl mx-auto border-t border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="hover:translate-y-[-5px] transition-transform duration-300">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-5 text-lg">⚡</div>
                <h3 class="text-sm font-bold mb-2 uppercase tracking-tight italic">Cepat & Ringan</h3>
                <p class="text-sm text-gray-500 leading-relaxed font-medium">Dibangun dengan teknologi terbaru Laravel Volt yang super kencang dan responsif.</p>
            </div>
            <div class="hover:translate-y-[-5px] transition-transform duration-300">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mb-5 text-lg">🔒</div>
                <h3 class="text-sm font-bold mb-2 uppercase tracking-tight italic">Aman Terkendali</h3>
                <p class="text-sm text-gray-500 leading-relaxed font-medium">Sistem login dan data kamu diproteksi dengan keamanan standar industri Laravel.</p>
            </div>
            <div class="hover:translate-y-[-5px] transition-transform duration-300">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mb-5 text-lg">📱</div>
                <h3 class="text-sm font-bold mb-2 uppercase tracking-tight italic">Mobile Friendly</h3>
                <p class="text-sm text-gray-500 leading-relaxed font-medium">Akses daftar tugasmu dari laptop, tablet, atau HP di mana saja dan kapan saja.</p>
            </div>
        </div>
    </section>

    <footer class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-8 flex flex-col md:flex-row justify-between items-center gap-8">

            <div class="flex items-center gap-3">
                <div class="w-7 h-7 bg-black rounded flex items-center justify-center text-white text-[10px] font-bold">
                    P
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm tracking-tight text-gray-900 leading-none">Proman.</span>
                    <span class="text-[9px] text-gray-400 font-medium tracking-tighter uppercase mt-1">Project Management System</span>
                </div>
            </div>

            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.15em]">
                &copy; {{ date('Y') }} — Built with Passion by <span class="text-gray-600">Scratching My Balls Inc.</span>
            </p>

            <div class="flex items-center gap-6">
                <a href="https://github.com/iqbaalmln10/MP_laravel" target="_blank" class="flex items-center gap-2 group text-gray-400 hover:text-black transition-colors duration-300">
                    <svg class="size-5 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">GitHub Repository</span>
                </a>
            </div>

        </div>
    </footer>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                // Style saat scroll: tipis & ada border
                navbar.classList.add('bg-white/90', 'backdrop-blur-md', 'border-b', 'border-gray-100', 'py-3');
                navbar.classList.remove('py-4');
            } else {
                // Style awal: transparan & lega
                navbar.classList.remove('bg-white/90', 'backdrop-blur-md', 'border-b', 'border-gray-100', 'py-3');
                navbar.classList.add('py-4');
            }
        });
    </script>

</body>

</html>