<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi - {{ config('app.name', 'Monitoring JPP') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="font-sans antialiased text-slate-800 bg-slate-50 selection:bg-[#2653e0] selection:text-white">

    <!-- Latar Belakang Luar: Gradasi abu-abu kebiruan untuk menonjolkan kartu putih -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-100 via-[#f0f4f8] to-slate-200 relative">
        
        <!-- Tombol Kembali ke Beranda Melayang (Floating Back Button) -->
        <a href="{{ route('home') }}" 
           title="Kembali ke Beranda"
           class="fixed top-4 left-4 sm:top-6 sm:left-6 z-40 inline-flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-white/90 hover:bg-white text-slate-600 hover:text-[#0f2b5c] border border-slate-200/90 shadow-sm hover:shadow-md backdrop-blur-md transition-all duration-200 group text-xs font-bold active:scale-95">
            <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0f2b5c] group-hover:-translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>

        <!-- Kartu Utama Melayang (Floating Card) dengan shadow elegan -->
        <div class="w-full max-w-[1000px] bg-white rounded-[2.5rem] shadow-2xl shadow-blue-900/15 ring-1 ring-slate-900/5 flex flex-col md:flex-row overflow-hidden min-h-[580px]">
            
            <!-- Sisi Kiri (Gambar Pabrik) -->
            <div class="w-full md:w-1/2 p-3 h-[42vh] md:h-auto shrink-0">
                <div class="w-full h-full rounded-[2rem] overflow-hidden relative bg-[#0f2b5c] shadow-inner group">
                    <img src="{{ asset('images/login2.png') }}" alt="Pabrik PT PIM" class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f2b5c]/80 via-[#0f2b5c]/20 to-transparent"></div>
                    
                    <div class="absolute bottom-10 left-10 right-10">
                        <div class="w-12 h-1 bg-white mb-6 opacity-80 rounded-full"></div>
                        <h1 class="text-3xl font-black text-white leading-tight mb-3">Monitoring<br>Jasa Pelayanan.</h1>
                        <p class="text-blue-100 text-sm font-medium opacity-90 leading-relaxed">
                            Akses sistem manajemen secara real-time, pantau progres pekerjaan, dan amankan data departemen di portal terpusat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan (Form Lupa Kata Sandi) -->
            <div class="w-full md:w-1/2 flex flex-col justify-center px-8 py-6 md:py-12 lg:px-16 relative bg-white rounded-b-[2.5rem] md:rounded-b-none">
                
                <!-- Ikon Kunci di atas judul -->
                <div class="mb-6 md:mb-8">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 border border-amber-200/60 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Lupa Kata Sandi?</h2>
                    <p class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                        Masukkan alamat email akun Departemen JPP Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                    </p>
                </div>

                <!-- Session Status Alert -->
                @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-bold flex items-center gap-2 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Alamat Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm focus:outline-none focus:border-[#2653e0] focus:ring-1 focus:ring-[#2653e0] transition-all font-medium"
                            placeholder="nama@pim.co.id">
                        @if ($errors->has('email'))
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#0f2b5c] hover:bg-[#0a1d40] text-white font-bold text-sm transition-all shadow-[0_8px_20px_-6px_rgba(15,43,92,0.5)] mt-4 active:scale-[0.98]">
                        Kirim Link Reset Password
                    </button>

                    <!-- Back to Login Link -->
                    <div class="text-center pt-3">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-[#0f2b5c] transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Kembali ke Halaman Login</span>
                        </a>
                    </div>

                    <!-- Footer Info -->
                    <div class="text-center mt-6">
                        <p class="text-[11px] text-slate-400 font-medium">© {{ date('Y') }} PT Pupuk Iskandar Muda</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>