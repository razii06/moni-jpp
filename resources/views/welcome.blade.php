<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Monitoring JPP - PT Pupuk Iskandar Muda') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-gradient-to-br from-slate-950 via-[#0a1e40] to-slate-900 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-amber-400 selection:text-slate-950 overflow-x-hidden">

    <!-- Header Navigation -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-20 relative">
        <div class="flex items-center gap-3">
            <div class="relative group">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-amber-400 to-emerald-400 rounded-2xl blur opacity-30 group-hover:opacity-75 transition duration-300">
                </div>
                <img src="{{ asset('images/logo-jpp.png') }}" alt="Logo JPP"
                    class="relative h-11 w-auto bg-white/10 p-2 rounded-xl backdrop-blur-md border border-white/20 shadow-inner">
            </div>
            <div>
                <span class="font-black text-lg tracking-tight text-white block leading-none">Monitoring JPP</span>
                <span class="text-[10px] text-amber-400 font-bold uppercase tracking-widest">PT Pupuk Iskandar
                    Muda</span>
            </div>
        </div>

        @if (Route::has('login'))
        <nav class="flex items-center gap-3">
            @auth
            <a href="{{ url('/home') }}"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-extrabold text-xs transition-all duration-300 shadow-lg shadow-amber-500/20 hover:-translate-y-0.5">
                Portal Monitoring
            </a>
            @else
            <a href="{{ route('login') }}"
                class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-md transition-all duration-300 hover:border-white/40">
                Login Staf
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition-all duration-300 shadow-md shadow-amber-500/20">
                Daftar
            </a>
            @endif
            @endauth
        </nav>
        @endif
    </header>

    <!-- Hero Content -->
    <main
        class="max-w-7xl mx-auto px-6 py-12 flex-1 flex flex-col justify-center items-center text-center relative z-10">
        <!-- Glowing Accents -->
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[560px] h-[560px] bg-blue-600/20 rounded-full blur-[130px] pointer-events-none animate-[pulse_8s_ease-in-out_infinite]">
        </div>
        <div
            class="absolute top-1/3 left-1/4 w-72 h-72 bg-amber-500/10 rounded-full blur-[100px] pointer-events-none animate-[pulse_6s_ease-in-out_infinite]">
        </div>
        <div
            class="absolute bottom-0 right-1/4 w-64 h-64 bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none">
        </div>

        <div
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-amber-300 border border-white/15 backdrop-blur-md mb-6">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400/60"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
            </span>
            Sistem Informasi & Transparansi Pekerjaan
        </div>

        <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight max-w-4xl">
            Sistem Monitoring Job Package <br class="hidden sm:inline" />Pekerjaan <span
                class="bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500 bg-clip-text text-transparent">Dept.
                JPP</span>
        </h1>

        <p class="mt-6 text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed font-normal">
            Platform terpadu untuk pemantauan progres, alokasi anggaran (OE), status Service Order (SO) & Purchase Order
            (PO) secara real-time dan transparan.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-4 items-center justify-center w-full sm:w-auto">
            <a href="{{ route('home') }}"
                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider transition-all duration-300 shadow-xl shadow-amber-500/25 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <span>Buka Dashboard Publik</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
            @guest
            <a href="{{ route('login') }}"
                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white/5 hover:bg-white/10 text-white font-bold text-xs uppercase tracking-wider border border-white/10 backdrop-blur-md transition-all duration-300 hover:border-white/25 flex items-center justify-center gap-2">
                <span>Login Internal</span>
            </a>
            @endguest
        </div>

        <!-- Fitur Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-16 w-full max-w-4xl text-left">
            <div
                class="group p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md space-y-2 hover:border-amber-400/30 hover:bg-white/[0.07] hover:-translate-y-1 transition-all duration-300">
                <div
                    class="w-10 h-10 rounded-xl bg-amber-400/10 text-amber-400 flex items-center justify-center font-black text-sm mb-3 border border-amber-400/20 group-hover:bg-amber-400 group-hover:text-slate-950 transition-all duration-300">
                    01</div>
                <h3 class="font-bold text-white text-base">Pantau Real-Time</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Perkembangan fisik dan persentase pencapaian proyek
                    diperbarui secara berkala oleh staf penanggung jawab.</p>
            </div>
            <div
                class="group p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md space-y-2 hover:border-blue-400/30 hover:bg-white/[0.07] hover:-translate-y-1 transition-all duration-300">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-400/10 text-blue-400 flex items-center justify-center font-black text-sm mb-3 border border-blue-400/20 group-hover:bg-blue-400 group-hover:text-slate-950 transition-all duration-300">
                    02</div>
                <h3 class="font-bold text-white text-base">Integrasi Dokumen</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Penyajian terstruktur untuk nomor Surat BAK/DOC,
                    Service Order (SO), PO, hingga estimasi Owner's Estimate (OE).</p>
            </div>
            <div
                class="group p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md space-y-2 hover:border-emerald-400/30 hover:bg-white/[0.07] hover:-translate-y-1 transition-all duration-300">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-400/10 text-emerald-400 flex items-center justify-center font-black text-sm mb-3 border border-emerald-400/20 group-hover:bg-emerald-400 group-hover:text-slate-950 transition-all duration-300">
                    03</div>
                <h3 class="font-bold text-white text-base">Akses Ringkas</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Antarmuka publik yang cepat diakses tanpa hambatan
                    serta portal autentikasi terproteksi bagi pengguna admin.</p>
            </div>
        </div>

        <!-- Scroll cue -->
        <div
            class="mt-14 flex flex-col items-center gap-2 text-slate-500 text-[10px] font-semibold uppercase tracking-widest">
            <span>Dept. JPP — PT Pupuk Iskandar Muda</span>
            <svg class="w-4 h-4 animate-bounce text-amber-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </main>

    <!-- Footer -->
    <footer
        class="w-full max-w-7xl mx-auto px-6 py-6 border-t border-white/10 text-slate-400 text-xs flex flex-col sm:flex-row items-center justify-between gap-4 z-20 relative">
        <p>© {{ date('Y') }} <span class="text-slate-200 font-semibold">PT Pupuk Iskandar Muda</span> — Departemen JPP.
        </p>
        <p class="text-[11px] text-slate-500">Sistem Informasi Monitoring Job Package Pekerjaan</p>
    </footer>

</body>

</html>