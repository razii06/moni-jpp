<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi - {{ config('app.name', 'Monitoring JPP') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-gradient-to-br from-slate-950 via-[#0a1e40] to-slate-900 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-amber-400 selection:text-slate-950">

    <!-- Glowing Background Accents -->
    <div
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[120px] pointer-events-none">
    </div>
    <div class="fixed top-1/3 left-1/3 w-72 h-72 bg-amber-500/10 rounded-full blur-[90px] pointer-events-none"></div>

    <!-- Header Navigation Ringkas -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between relative z-20">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="relative">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-amber-400 to-emerald-400 rounded-2xl blur opacity-30 group-hover:opacity-75 transition duration-300">
                </div>
                <img src="{{ asset('images/logo-jpp.png') }}" alt="Logo JPP"
                    class="relative h-10 w-auto bg-white/10 p-2 rounded-xl backdrop-blur-md border border-white/20">
            </div>
            <div>
                <span class="font-black text-base tracking-tight text-white block leading-none">Monitoring JPP</span>
                <span class="text-[9px] text-amber-400 font-bold uppercase tracking-widest">PT Pupuk Iskandar
                    Muda</span>
            </div>
        </a>
        <a href="{{ url('/') }}"
            class="text-xs text-slate-400 hover:text-white transition flex items-center gap-1 font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </header>

    <!-- Main Card Reset Password -->
    <main class="w-full max-w-md mx-auto px-6 py-8 relative z-10 my-auto">
        <div
            class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-emerald-400 to-blue-500">
            </div>

            <div class="text-center mb-6">
                <h2 class="text-2xl font-black text-white tracking-tight">Lupa Kata Sandi?</h2>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Masukkan alamat email akun Departemen JPP Anda. Kami akan mengirimkan tautan untuk mengatur ulang
                    kata sandi.
                </p>
            </div>

            <!-- Session Status Alert -->
            @if (session('status'))
            <div
                class="mb-4 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email"
                        class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Alamat
                        Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/15 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition"
                        placeholder="nama@pim.co.id">
                    @if ($errors->has('email'))
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider transition duration-300 shadow-lg shadow-amber-500/20 active:scale-[0.98]">
                    Kirim Link Reset Password
                </button>

                <!-- Back to Login Link -->
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}"
                        class="text-xs font-semibold text-amber-400 hover:text-amber-300 transition">
                        &larr; Kembali ke Halaman Login
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer Ringkas -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-6 text-center text-slate-500 text-xs relative z-20">
        © {{ date('Y') }} PT Pupuk Iskandar Muda — Departemen JPP
    </footer>

</body>

</html>