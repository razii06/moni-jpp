<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Monitoring JPP') }}</title>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .login-feedback { position: fixed; inset: 0; z-index: 50; display: none; align-items: center; justify-content: center; background: rgba(15,43,92,.94); backdrop-filter: blur(10px); }
        .login-feedback.is-visible { display: flex; animation: feedbackIn .25s ease both; }
        .feedback-content { display:flex; flex-direction:column; align-items:center; gap:12px; color:#fff; text-align:center; }
        .feedback-icon { width:78px; height:78px; display:grid; place-items:center; border-radius:50%; color:#fff; font-size:42px; font-weight:900; box-shadow:0 0 32px rgba(16,185,129,.45); animation: feedbackPop .45s cubic-bezier(.175,.885,.32,1.275) both; }
        .feedback-icon.success { background:#10b981; }
        .feedback-icon.failed { background:#ef4444; box-shadow:0 0 32px rgba(239,68,68,.45); }
        .feedback-content h3 { margin:0; font-size:1.55rem; font-weight:900; }
        .feedback-content p { margin:0; color:#cbd5e1; font-size:.86rem; }
        @keyframes feedbackIn { from { opacity:0; } to { opacity:1; } }
        @keyframes feedbackPop { from { opacity:0; transform:scale(.5); } to { opacity:1; transform:scale(1); } }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 selection:bg-[#2653e0] selection:text-white">

    <!-- Feedback Modal -->
    <div id="loginFeedback" class="login-feedback" aria-live="polite">
        <div class="feedback-content">
            <div id="feedbackIcon" class="feedback-icon success">✓</div>
            <h3 id="feedbackTitle">Login berhasil!</h3>
            <p id="feedbackText">Membuka Dashboard...</p>
        </div>
    </div>

    <!-- Latar Belakang Luar: Putih dengan gradasi biru sangat muda dan padding -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-white via-blue-50/50 to-indigo-50/40">
        
        <!-- Kartu Utama Melayang (Floating Card) -->
        <div class="w-full max-w-[1000px] bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)] flex flex-col md:flex-row overflow-hidden border border-slate-100 min-h-[580px]">
            
            <!-- Sisi Kiri (Gambar Pabrik) -->
            <!-- p-3 memberikan jarak agar gambar tidak menabrak batas kartu dan terlihat seperti bingkai -->
            <!-- Di layar HP, gambar akan memiliki tinggi tetap 30vh (h-[30vh]) dan tidak boleh menyusut (shrink-0) -->
            <div class="w-full md:w-1/2 p-3 h-[30vh] md:h-auto shrink-0">
                <div class="w-full h-full rounded-[2rem] overflow-hidden relative bg-[#0f2b5c] shadow-inner group">
                    <!-- Gunakan login2.png. object-cover memastikan gambar terisi penuh pada area berbingkai -->
                    <img src="{{ asset('images/login2.png') }}" alt="Pabrik PT PIM" class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                    
                    <!-- Overlay gradien tipis untuk meredupkan bagian bawah agar teks terbaca -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f2b5c]/80 via-[#0f2b5c]/20 to-transparent"></div>
                    
                    <!-- Teks di dalam gambar mirip dengan desain referensi ("Ticketed.") -->
                    <div class="absolute bottom-10 left-10 right-10">
                        <div class="w-12 h-1 bg-white mb-6 opacity-80 rounded-full"></div>
                        <h1 class="text-3xl font-black text-white leading-tight mb-3">Monitoring<br>Jasa Pelayanan.</h1>
                        <p class="text-blue-100 text-sm font-medium opacity-90 leading-relaxed">
                            Akses sistem manajemen secara real-time, pantau progres pekerjaan, dan amankan data departemen di portal terpusat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan (Form Login) -->
            <div class="w-full md:w-1/2 flex flex-col justify-center px-8 py-12 lg:px-16 relative bg-white">
                
                <!-- Ikon Kecil di atas judul -->
                <div class="mb-8">
                    <div class="w-12 h-12 bg-blue-50 text-[#2653e0] rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Masuk ke Akun</h2>
                    <p class="text-sm text-slate-500 mt-2 font-medium">Gunakan kredensial internal Departemen JPP Anda.</p>
                </div>

                @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-bold flex items-center gap-2 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-bold text-slate-700 mb-1.5">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:border-[#2653e0] focus:ring-1 focus:ring-[#2653e0] transition-all font-medium"
                            placeholder="Contoh: admin_jpp">
                        @if ($errors->has('username'))
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $errors->first('username') }}</p>
                        @endif
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-[#2653e0] hover:text-blue-800 font-bold transition">Lupa kata sandi?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:border-[#2653e0] focus:ring-1 focus:ring-[#2653e0] transition-all font-medium"
                            placeholder="••••••••">
                        @if ($errors->has('password'))
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Google reCAPTCHA Widget -->
                    <div class="flex justify-center pt-2">
                        <div class="g-recaptcha" data-theme="light" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    </div>

                    <!-- Submit Button -->
                    <!-- Menggunakan warna biru solid yang terang mirip dengan referensi -->
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#3949ff] hover:bg-[#2836d1] text-white font-bold text-sm transition-all shadow-[0_8px_20px_-6px_rgba(57,73,255,0.4)] mt-4 active:scale-[0.98]">
                        Masuk ke Sistem
                    </button>
                    
                    <!-- Footer Info -->
                    <div class="text-center mt-6">
                        <p class="text-[11px] text-slate-400 font-medium">© {{ date('Y') }} PT Pupuk Iskandar Muda</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script AJAX Handler -->
    <script>
    (() => {
        const form = document.querySelector('form[action="{{ route('login') }}"]');
        const feedback = document.getElementById('loginFeedback');
        const icon = document.getElementById('feedbackIcon');
        const title = document.getElementById('feedbackTitle');
        const text = document.getElementById('feedbackText');
        const errors = @json($errors->all());

        const showFeedback = (success, message) => {
            icon.className = `feedback-icon ${success ? 'success' : 'failed'}`;
            icon.textContent = success ? '✓' : '×';
            title.textContent = success ? 'Login berhasil!' : 'Login gagal';
            text.textContent = message;
            feedback.classList.add('is-visible');
        };

        if (errors.length) {
            showFeedback(false, errors[0]);
            setTimeout(() => feedback.classList.remove('is-visible'), 2200);
        }

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            showFeedback(true, 'Memverifikasi akun...');
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (response.ok || response.redirected) {
                    showFeedback(true, 'Membuka Dashboard...');
                    setTimeout(() => { window.location.href = '{{ route('admin.dashboard') }}'; }, 850);
                    return;
                }
                const payload = await response.json().catch(() => ({}));
                const message = payload?.errors ? Object.values(payload.errors).flat()[0] : 'Username atau password tidak sesuai.';
                showFeedback(false, message);
                if (typeof grecaptcha !== 'undefined') { grecaptcha.reset(); }
                setTimeout(() => feedback.classList.remove('is-visible'), 2200);
            } catch (error) {
                showFeedback(false, 'Server tidak dapat dihubungi. Coba lagi.');
                if (typeof grecaptcha !== 'undefined') { grecaptcha.reset(); }
                setTimeout(() => feedback.classList.remove('is-visible'), 2200);
            }
        });
    })();
    </script>
</body>
</html>