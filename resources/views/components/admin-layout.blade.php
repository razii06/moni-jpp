<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - Monitoring JPP</title>
    
    <!-- Kita matikan sementara Vite-nya untuk membypass terminal -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    
    <!-- Tailwind CDN agar styling langsung jalan -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Animasi Mulus -->
    <style>
        @keyframes adminPagePop {
            0% { opacity: 0; transform: scale(0.95) translateY(16px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .admin-pop-animate {
            animation: adminPagePop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            will-change: transform, opacity;
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
                @endif
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
            <div x-data="{ tampil: false }" 
                x-init="setTimeout(() => tampil = true, 50)"
                class="transform transition-all duration-700 ease-out"
                :class="tampil ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'">
                
                {{ $slot }}
                
            </div>
        </main>
    </div>

</body>

</html>