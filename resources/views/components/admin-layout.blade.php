<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - Monitoring JPP</title>
    
    <!-- Kita matikan sementara Vite-nya untuk membypass terminal -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    
    <!-- Google Fonts: Inter (Standar Modern SaaS UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Animasi Mulus & Font Setting -->
    <style>
        body, input, select, textarea, button {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        @keyframes adminPagePop {
            0% { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: none; }
        }
        .admin-pop-animate {
            animation: adminPagePop 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Custom Styling Modern untuk SweetAlert2 Modal (HANYA aktif saat modal pop-up tengah terbuka) */
        body.swal2-shown:not(.swal2-toast-shown) .swal2-container.swal2-backdrop-show,
        .swal2-container.swal2-center.swal2-backdrop-show {
            background: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }

        /* Pastikan Toast Notifikasi di pojok kanan SELALU transparan tanpa bayangan hitam & tanpa blur */
        body.swal2-toast-shown .swal2-container,
        .swal2-container.swal2-top-end,
        .swal2-container:has(.swal2-toast) {
            background: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            pointer-events: none !important;
        }
        .swal2-toast {
            pointer-events: auto !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 1rem !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }

        .swal2-popup.jpp-swal-modal {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            border-radius: 1.25rem !important;
            padding: 1.75rem 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }
        .swal2-icon.swal2-warning {
            border-color: #f43f5e !important;
            color: #f43f5e !important;
        }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <!-- Overlay Latar Belakang saat Sidebar Buka di HP -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    <!-- Sidebar Responsif (Sekarang Berwarna Putih) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between shadow-xl lg:shadow-none">

        <div>
            <!-- Header Sidebar (Logo & Brand) -->
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo JPP" class="h-10 w-auto">
                    <div>
                        <h1 class="font-extrabold text-sm tracking-wide text-blue-950 leading-tight">Monitoring JPP</h1>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">PT Pupuk Iskandar Muda</p>
                    </div>
                </div>
                <!-- Tombol Close Sidebar (Hanya HP) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigasi Menu Dinamis -->
            <nav class="p-4 space-y-1.5">
                <span class="px-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2 mt-2">MENU</span>
                
                <!-- Menu Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f2b5c] text-white font-bold border-l-4 border-amber-400' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-900 font-semibold' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Menu Job Package -->
                <a href="{{ route('admin.job-packages.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm {{ request()->routeIs('admin.job-packages.*') ? 'bg-[#0f2b5c] text-white font-bold border-l-4 border-amber-400' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-900 font-semibold' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.job-packages.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <span>Job Package</span>
                </a>

                <!-- Menu Dokumentasi Beranda -->
                <a href="{{ route('admin.dokumentasi.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm {{ request()->routeIs('admin.dokumentasi*') ? 'bg-[#0f2b5c] text-white font-bold border-l-4 border-amber-400' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-900 font-semibold' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.dokumentasi*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm4 0v14m4-10h4m-4 4h4" />
                    </svg>
                    <span>Dokumentasi Beranda</span>
                </a>

                <!-- Menu User Management (Khusus Admin) -->
                @if(auth()->check() && (auth()->user()->role === 'admin' || (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())))
                <span class="px-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2 mt-6">ADMINISTRASI</span>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm {{ request()->routeIs('admin.users.*') ? 'bg-[#0f2b5c] text-white font-bold border-l-4 border-amber-400' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-900 font-semibold' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>User Management</span>
                </a>

                <a href="{{ route('admin.system-logs.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm {{ request()->routeIs('admin.system-logs.*') ? 'bg-[#0f2b5c] text-white font-bold border-l-4 border-amber-400' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-900 font-semibold' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.system-logs.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>System Logs (Jejak)</span>
                </a>
                @endif

                <span class="px-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2 mt-6">KEAMANAN AKUN</span>
                
                <!-- Menu 2FA Setup -->
                <a href="{{ route('admin.2fa.setup') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm {{ request()->routeIs('admin.2fa.setup') ? 'bg-[#0f2b5c] text-white font-bold border-l-4 border-amber-400' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-900 font-semibold' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.2fa.setup') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Pengaturan 2FA</span>
                </a>
            </nav>
        </div>

        <!-- Footer Sidebar dengan Profil & Logout -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center justify-between gap-2 px-3 py-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 rounded-lg bg-[#0f2b5c] flex items-center justify-center font-bold text-white text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@pim.co.id' }}</p>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Logout"
                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Content Wrapper Utama -->
    <div class="lg:pl-64 flex flex-col min-h-screen">

        <!-- Topbar Mobile & Desktop Header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 py-3.5 lg:px-8 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Tombol Hamburger (Mobile & Tablet) -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-xl bg-slate-50 text-slate-600 lg:hidden hover:bg-slate-100 border border-slate-200 focus:outline-none transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-xs font-bold px-3 py-1 bg-amber-50 text-amber-600 rounded-full border border-amber-200/60 hidden sm:inline-block shadow-sm">
                    Monitoring System v2.0
                </span>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-800">Jasa Pelayanan Pabrik</p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">PT Pupuk Iskandar Muda</p>
                </div>
            </div>
        </header>

        <!-- Main Content Area dengan Animasi Masuk -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            <div class="admin-pop-animate">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- SweetAlert2 untuk Flash Messages Interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Konfigurasi Toast untuk pesan sukses ringan
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            // Tangkap session sukses (biasanya untuk aksi simpan/ubah)
            @if(session('success') || session('status'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session("success") ?? session("status") }}'
                });
            @endif

            // Tangkap session error (biasanya untuk akses ditolak atau gagal proses krusial)
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Terdapat Masalah!',
                    text: '{{ session("error") }}',
                    confirmButtonColor: '#0f2b5c',
                    shape: 'border-radius: 1rem;'
                });
            @endif

            // Tangkap session warning (misal data tersimpan tetapi ada berkas gagal diunggah)
            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    iconColor: '#f59e0b',
                    title: 'Perhatian!',
                    text: '{{ session("warning") }}',
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#f59e0b',
                    customClass: {
                        popup: 'jpp-swal-modal font-sans max-w-sm sm:max-w-md',
                        title: '!text-base sm:!text-lg !font-bold !text-slate-800 !pt-1 !tracking-tight',
                        confirmButton: 'py-2.5 px-6 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-900 bg-amber-400 hover:bg-amber-500 transition-all duration-150 cursor-pointer shadow-md'
                    }
                });
            @endif

            // Tangkap semua validation error sekaligus (misal lupa isi form)
            @if(isset($errors) && $errors->any())
                let errorHtml = '<ul style="text-align: left; font-size: 0.875rem; color: #ef4444; list-style-type: disc; padding-left: 1.5rem;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li style="margin-bottom: 0.25rem;">{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: errorHtml,
                    confirmButtonColor: '#0f2b5c',
                });
            @endif
            
            // Pulihkan posisi scroll setelah reload jika ada posisi yang tersimpan (Smart Scroll Memory)
            const savedScrollAnchor = sessionStorage.getItem('moni_jpp_scroll_anchor');
            const savedScrollPos = sessionStorage.getItem('moni_jpp_scroll_pos');

            if (savedScrollAnchor) {
                sessionStorage.removeItem('moni_jpp_scroll_anchor');
                sessionStorage.removeItem('moni_jpp_scroll_pos');
                setTimeout(() => {
                    const el = document.getElementById(savedScrollAnchor);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            } else if (savedScrollPos !== null) {
                sessionStorage.removeItem('moni_jpp_scroll_pos');
                const targetY = parseInt(savedScrollPos, 10);
                if (!isNaN(targetY) && targetY > 0) {
                    window.scrollTo({ top: targetY, behavior: 'instant' });
                    setTimeout(() => {
                        window.scrollTo({ top: targetY, behavior: 'instant' });
                    }, 50);
                }
            }

            // Modal Konfirmasi Modern Enterprise (SweetAlert2 Custom Theme)
            window.showConfirmDialog = function(options) {
                const config = Object.assign({
                    title: 'Hapus Data Permanen?',
                    itemName: '',
                    message: 'Data yang dihapus tidak dapat dipulihkan kembali.',
                    confirmText: 'Ya, Hapus Permanen',
                    cancelText: 'Batal',
                    confirmButtonClass: 'bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white shadow-md shadow-rose-600/20',
                    icon: 'warning',
                    iconColor: '#f43f5e',
                    onConfirm: () => {}
                }, options);

                let htmlContent = '<div class="text-xs text-slate-500 leading-relaxed text-center">';
                if (config.itemName) {
                    htmlContent += '<div class="mb-3 p-3 bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 font-semibold text-xs leading-normal break-words text-center">&ldquo;' + config.itemName + '&rdquo;</div>';
                }
                htmlContent += '<p class="text-slate-500 font-normal">' + config.message + '</p></div>';

                Swal.fire({
                    title: config.title,
                    html: htmlContent,
                    icon: config.icon,
                    iconColor: config.iconColor,
                    showCancelButton: true,
                    confirmButtonText: config.confirmText,
                    cancelButtonText: config.cancelText,
                    reverseButtons: true,
                    focusCancel: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'jpp-swal-modal font-sans max-w-sm sm:max-w-md',
                        title: '!text-base sm:!text-lg !font-bold !text-slate-800 !pt-1 !tracking-tight',
                        htmlContainer: '!mt-2 !mb-0 !overflow-visible',
                        actions: '!flex !gap-3 !w-full !mt-6 !justify-center',
                        confirmButton: 'flex-1 py-2.5 px-4 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-150 active:scale-95 cursor-pointer ' + config.confirmButtonClass,
                        cancelButton: 'flex-1 py-2.5 px-4 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-800 border border-slate-200 transition-all duration-150 active:scale-95 cursor-pointer'
                    }
                }).then((result) => {
                    if (result.isConfirmed && typeof config.onConfirm === 'function') {
                        config.onConfirm();
                    }
                });
            };

            // Helper backward-compatible untuk tombol dengan confirmDelete(...)
            window.confirmDelete = function(formId, itemName, title, message) {
                window.showConfirmDialog({
                    title: title || 'Hapus Data Permanen?',
                    itemName: itemName || '',
                    message: message || 'Data yang dihapus tidak dapat dipulihkan kembali.',
                    onConfirm: () => {
                        sessionStorage.setItem('moni_jpp_scroll_pos', window.scrollY);
                        const form = typeof formId === 'string' ? document.getElementById(formId) : formId;
                        if (form) form.submit();
                    }
                });
            };

            // Interseptor otomatis untuk semua form dengan atribut data-confirm-delete
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form && form.hasAttribute('data-confirm-delete')) {
                    e.preventDefault();
                    
                    const title = form.getAttribute('data-confirm-title') || 'Hapus Data Permanen?';
                    const itemName = form.getAttribute('data-item-name') || '';
                    const message = form.getAttribute('data-confirm-delete') || 'Data yang dihapus tidak dapat dipulihkan kembali.';
                    const confirmBtn = form.getAttribute('data-confirm-btn') || 'Ya, Hapus Permanen';
                    const confirmType = form.getAttribute('data-confirm-type') || 'danger';
                    const scrollAnchor = form.getAttribute('data-scroll-anchor') || '';
                    
                    let icon = 'warning';
                    let iconColor = '#f43f5e';
                    let confirmButtonClass = 'bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white shadow-md shadow-rose-600/20';

                    if (confirmType === 'warning') {
                        icon = 'warning';
                        iconColor = '#f59e0b';
                        confirmButtonClass = 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-md shadow-amber-500/20';
                    } else if (confirmType === 'success') {
                        icon = 'question';
                        iconColor = '#10b981';
                        confirmButtonClass = 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-md shadow-emerald-500/20';
                    }

                    window.showConfirmDialog({
                        title: title,
                        itemName: itemName,
                        message: message,
                        confirmText: confirmBtn,
                        icon: icon,
                        iconColor: iconColor,
                        confirmButtonClass: confirmButtonClass,
                        onConfirm: () => {
                            if (scrollAnchor) {
                                sessionStorage.setItem('moni_jpp_scroll_anchor', scrollAnchor);
                            } else {
                                sessionStorage.setItem('moni_jpp_scroll_pos', window.scrollY);
                            }
                            form.removeAttribute('data-confirm-delete');
                            form.submit();
                        }
                    });
                } else if (form && (form.hasAttribute('data-preserve-scroll') || form.classList.contains('preserve-scroll'))) {
                    const scrollAnchor = form.getAttribute('data-scroll-anchor');
                    if (scrollAnchor) {
                        sessionStorage.setItem('moni_jpp_scroll_anchor', scrollAnchor);
                    } else {
                        sessionStorage.setItem('moni_jpp_scroll_pos', window.scrollY);
                    }
                }
            });
        });
    </script>
</body>

</html>