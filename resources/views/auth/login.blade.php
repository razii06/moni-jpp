<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Staf - {{ config('app.name', 'Monitoring JPP') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body.login-reference {
            background: linear-gradient(rgba(0, 0, 0, .42), rgba(0, 0, 0, .42)), url('{{ asset('images/jpp.jpg') }}') center / cover fixed;
        }
        .login-reference .login-card {
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            animation: loginCardIn .8s cubic-bezier(.2, .8, .2, 1) both;
        }
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
        @keyframes loginCardIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @media (prefers-reduced-motion: reduce) { .login-reference .login-card { animation: none; } }
    </style>
</head>

<body
    class="login-reference bg-gradient-to-br from-slate-950 via-[#0a1e40] to-slate-900 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-amber-400 selection:text-slate-950">

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

        <div id="loginFeedback" class="login-feedback" aria-live="polite">
            <div class="feedback-content">
                <div id="feedbackIcon" class="feedback-icon success">✓</div>
                <h3 id="feedbackTitle">Login berhasil!</h3>
                <p id="feedbackText">Membuka Dashboard Admin...</p>
            </div>
        </div>

    <!-- Main Login Card -->
    <main class="w-full max-w-md mx-auto px-6 py-8 relative z-10 my-auto">
        <div
            class="login-card bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-emerald-400 to-blue-500">
            </div>

            <div class="text-center mb-6">
                <h2 class="text-2xl font-black text-white tracking-tight">Portal Akses Staf</h2>
                <p class="text-xs text-slate-400 mt-1">Masukkan akun internal Departemen JPP Anda</p>
            </div>

            <!-- Session Status Alert -->
            @if (session('status'))
            <div
                class="mb-4 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Username Field -->
                <div>
                    <label for="username"
                        class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/15 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition"
                        placeholder="Masukkan username">
                    @if ($errors->has('username'))
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $errors->first('username') }}</p>
                    @endif
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password"
                            class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Kata Sandi</label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs text-amber-400 hover:text-amber-300 transition">Lupa kata sandi?</a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/15 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition"
                        placeholder="••••••••">
                    @if ($errors->has('password'))
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-white/20 bg-slate-900/80 text-amber-500 focus:ring-amber-400 focus:ring-offset-slate-950">
                    <label for="remember_me" class="ml-2 text-xs text-slate-300">Ingat saya di perangkat ini</label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider transition duration-300 shadow-lg shadow-amber-500/20 active:scale-[0.98]">
                    Masuk ke Sistem
                </button>
            </form>
        </div>
    </main>

    <!-- Footer Ringkas -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-6 text-center text-slate-500 text-xs relative z-20">
        © {{ date('Y') }} PT Pupuk Iskandar Muda — Departemen JPP
    </footer>

</body>

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
                    showFeedback(true, 'Membuka Dashboard Admin...');
                    setTimeout(() => { window.location.href = '{{ route('admin.dashboard') }}'; }, 850);
                    return;
                }
                const payload = await response.json().catch(() => ({}));
                const message = payload?.errors ? Object.values(payload.errors).flat()[0] : 'Username atau password tidak sesuai.';
                showFeedback(false, message);
                setTimeout(() => feedback.classList.remove('is-visible'), 2200);
            } catch (error) {
                showFeedback(false, 'Server tidak dapat dihubungi. Coba lagi.');
                setTimeout(() => feedback.classList.remove('is-visible'), 2200);
            }
        });
    })();
</script>

</html>