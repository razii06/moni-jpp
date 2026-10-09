<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi 2FA - {{ config('app.name', 'Monitoring JPP') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 selection:bg-[#2653e0] selection:text-white">

    <!-- Latar Belakang Luar: Putih dengan gradasi biru sangat muda dan padding -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-white via-blue-50/50 to-indigo-50/40">
        
        <!-- Kartu Utama Melayang (Floating Card) -->
        <div class="w-full max-w-[1000px] bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)] flex flex-col md:flex-row overflow-hidden border border-slate-100 min-h-[580px]">
            
            <!-- Sisi Kiri (Gambar Pabrik) -->
            <!-- p-3 memberikan jarak agar gambar tidak menabrak batas kartu dan terlihat seperti bingkai -->
            <div class="w-full md:w-1/2 p-3 h-[30vh] md:h-auto shrink-0">
                <div class="w-full h-full rounded-[2rem] overflow-hidden relative bg-[#0f2b5c] shadow-inner group">
                    <img src="{{ asset('images/login2.png') }}" alt="Pabrik PT PIM" class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f2b5c]/80 via-[#0f2b5c]/20 to-transparent"></div>
                    
                    <div class="absolute bottom-10 left-10 right-10">
                        <div class="w-12 h-1 bg-white mb-6 opacity-80 rounded-full"></div>
                        <h1 class="text-3xl font-black text-white leading-tight mb-3">Keamanan<br>Berlapis.</h1>
                        <p class="text-blue-100 text-sm font-medium opacity-90 leading-relaxed">
                            Sistem otentikasi dua faktor diaktifkan untuk melindungi data rahasia departemen dan perusahaan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan (Form Verifikasi 2FA) -->
            <div class="w-full md:w-1/2 flex flex-col justify-center px-8 py-12 lg:px-16 relative bg-white">
                
                <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="absolute top-8 right-8 text-sm font-bold text-rose-500 hover:text-rose-700 transition">
                    Batal & Keluar
                </a>

                <!-- Ikon Kecil di atas judul -->
                <div class="mb-8 mt-4">
                    <div class="w-12 h-12 bg-blue-50 text-[#2653e0] rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Verifikasi 2FA</h2>
                    <p class="text-sm text-slate-500 mt-2 font-medium">Buka aplikasi Google Authenticator Anda dan masukkan 6 digit kode OTP.</p>
                </div>

                <form method="POST" action="{{ route('2fa.verify.post') }}" class="space-y-6">
                    @csrf
                    <div>
                        <input type="text" name="otp" required autofocus maxlength="6" autocomplete="one-time-code"
                            class="w-full px-4 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-center text-3xl tracking-[0.5em] font-mono font-black focus:outline-none focus:border-[#2653e0] focus:ring-2 focus:ring-blue-100 transition-all shadow-inner"
                            placeholder="••••••">
                        @if ($errors->has('otp'))
                            <p class="mt-2 text-sm text-center text-rose-500 font-bold">{{ $errors->first('otp') }}</p>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-4 px-4 rounded-xl bg-[#3949ff] hover:bg-[#2836d1] text-white font-black text-sm tracking-widest uppercase transition-all shadow-[0_8px_20px_-6px_rgba(57,73,255,0.4)] mt-4 active:scale-[0.98]">
                        Verifikasi Kode
                    </button>
                    
                    <div class="text-center mt-8">
                        <p class="text-[11px] text-slate-400 font-medium">© {{ date('Y') }} PT Pupuk Iskandar Muda</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
