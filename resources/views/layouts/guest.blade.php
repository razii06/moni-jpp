    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Monitoring Pekerjaan JPP - PT Pupuk Iskandar Muda' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            html { scroll-behavior: smooth; }
            body.public-reference { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
            .public-reference .kpi-icon-3d { box-shadow: 0 10px 0 rgba(37,99,235,.10), 0 16px 22px rgba(15,43,92,.12); transform: translateY(-1px); transition: transform .25s ease, box-shadow .25s ease; }
            .public-reference .group:hover .kpi-icon-3d { transform: translateY(-5px) rotate(-3deg); box-shadow: 0 13px 0 rgba(37,99,235,.12), 0 21px 28px rgba(15,43,92,.15); }
            .public-reference .info-section { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 20px; }
            .public-reference .info-card { min-height: 176px; padding: 24px; border: 1px solid #e5ebf3; border-radius: 16px; background: #fff; box-shadow: 0 6px 22px rgba(15,23,42,.045); transition: transform .25s ease, box-shadow .25s ease; }
            .public-reference .info-card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(15,23,42,.10); }
            .public-reference .info-card h4 { display:flex; align-items:center; gap:8px; margin:0 0 10px; color:#0b1120; font-size:1rem; font-weight:800; }
            .public-reference .info-card p { margin:0 0 18px; color:#64748b; font-size:.78rem; line-height:1.6; }
            .public-reference .info-card a { color:#1e3a8a; font-size:.78rem; font-weight:800; text-decoration:underline; }
            .public-reference .info-card.external { color:#fff; background:linear-gradient(135deg,#1e3a8a,#0b1120); border:0; }
            .public-reference .info-card.external h4, .public-reference .info-card.external p { color:#fff; }
            .public-reference .info-card.external p { color:#cbd5e1; }
            .public-reference .info-card.external a { color:#f2b818; }
            @media (max-width: 800px) { .public-reference .info-section { grid-template-columns:1fr; } }
            /* Efek Cahaya Glow dan Kilatan Sweep */
            .btn-glow {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            .btn-glow:hover {
                transform: translateY(-2px) scale(1.03);
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.7) !important;
            }
            .btn-glow::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                transition: left 0.7s ease;
            }
            .btn-glow:hover::before {
                left: 100%;
            }
        </style>
    </head>

    <body class="public-reference bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col justify-between">

        <!-- Public Header / Navbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12">
                <div class="flex justify-between h-16 items-center">

                    <!-- Branding Logo Gambar & Judul -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <img src="{{ asset('logo.png') }}" alt="Logo JPP" class="h-10 w-auto object-contain">
                        <div>
                            <span class="text-sm font-black text-slate-900 block leading-tight">Monitoring JPP</span>
                            <span class="text-[10px] font-bold text-slate-400 block tracking-wide uppercase">PT Pupuk Iskandar Muda</span>
                        </div>
                    </a>

                    <!-- Tombol Dinamis: Dashboard Admin (Jika Sudah Login) / Login Admin (Jika Belum Login) -->
                    @auth
                        <a href="{{ route('admin.dashboard') }}" 
                        class="btn-glow inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-md border border-amber-400/30"
                        style="background-color: #0f2b5c;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Dashboard Admin</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                        class="btn-glow inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-md border border-amber-400/30"
                        style="background-color: #0f2b5c;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l4-4m0 0l-4-4m4 4H3m6 4v1a3 3 0 003 3h7a3 3 0 003-3V7a3 3 0 00-3-3h-7a3 3 0 00-3 3v1" />
                            </svg>
                            <span>Login Admin</span>
                        </a>
                    @endauth

                </div>
            </div>
        </header>

        <!-- Slot Konten Utama -->
        <main class="w-full flex-1">
            {{ $slot }}
        </main>

        <!-- Public Footer -->
        <footer class="bg-white border-t border-slate-200 text-slate-400 text-xs py-6 text-center mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>© {{ date('Y') }} <span class="font-bold text-slate-600">PT Pupuk Iskandar Muda</span> — Departemen JPP.</p>
                <p class="text-[11px] text-slate-400">Sistem Monitoring Progres Transparan & Real-Time</p>
            </div>
        </footer>

    </body>

    </html>