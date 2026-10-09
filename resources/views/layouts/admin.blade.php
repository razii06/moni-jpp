    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Admin Dashboard' }} - Monitoring JPP</title>

        <!-- Tailwind CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Alpine.js untuk toggle mobile menu -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <!-- ApexCharts -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- Animasi Halaman Global Murni (CSS GPU Engine) -->
        <style>
            @keyframes pagePop {
                0% {
                    opacity: 0;
                    transform: translateY(16px) scale(0.98);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .animate-page-pop {
                animation: pagePop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                will-change: transform, opacity;
            }
        </style>
    </head>

    <body class="bg-[#f8fafc] font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

        <div class="min-h-screen flex flex-col md:flex-row relative overflow-x-hidden">

            <!-- Mobile Header Bar -->
            <header class="md:hidden bg-[#0f2b5c] text-white px-4 py-3 flex items-center justify-between shadow-md sticky top-0 z-40">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('logo.png') }}" alt="Logo JPP" class="h-10 w-auto">
                    <div>
                        <h1 class="font-black text-xs leading-tight tracking-wide">Monitoring JPP</h1>
                        <p class="text-[9px] text-amber-400 font-bold uppercase tracking-wider">PT Pupuk Iskandar Muda</p>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <!-- Sidebar Overlay (Mobile) -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden" style="display: none;"></div>

            <!-- Sidebar Navigation -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white min-h-screen border-r border-slate-200/80 flex flex-col justify-between shrink-0 font-sans shadow-2xl md:shadow-sm transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0">
                <div class="p-5">
                    <!-- Logo Header -->
                    <div class="mb-8 px-2">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('logo.png') }}" alt="Logo JPP" class="h-10 w-auto">
                            <div>
                                <h1 class="text-sm font-extrabold text-blue-950 leading-tight">Monitoring JPP</h1>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PT Pupuk Iskandar Muda</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="space-y-6">
                        <!-- Group 1: MENU -->
                        <div>
                            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">MENU</span>
                            <div class="space-y-1">
                                <!-- Dashboard -->
                                <a href="{{ route('admin.dashboard') }}" 
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f2b5c] text-white shadow-sm border-l-4 border-amber-400' : 'text-slate-600 hover:text-blue-900 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                    <span>Dashboard</span>
                                </a>

                                <!-- Job Package -->
                                <a href="{{ route('admin.job-packages.index') }}" 
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition-all {{ request()->routeIs('admin.job-packages.*') ? 'bg-[#0f2b5c] text-white shadow-sm border-l-4 border-amber-400' : 'text-slate-600 hover:text-blue-900 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('admin.job-packages.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                    <span>Job Package</span>
                                </a>

                                <!-- Dokumentasi Beranda -->
                                <a href="{{ route('admin.dokumentasi.index') }}" 
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition-all {{ request()->routeIs('admin.dokumentasi.*') ? 'bg-[#0f2b5c] text-white shadow-sm border-l-4 border-amber-400' : 'text-slate-600 hover:text-blue-900 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('admin.dokumentasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Dokumentasi Beranda</span>
                                </a>
                            </div>
                        </div>

                        <!-- Group 2: ADMINISTRASI -->
                        <div>
                            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">ADMINISTRASI</span>
                            <div class="space-y-1">
                                <!-- User Management -->
                                <a href="{{ route('admin.users.index') }}" 
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#0f2b5c] text-white shadow-sm border-l-4 border-amber-400' : 'text-slate-600 hover:text-blue-900 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>Manajemen Pengguna</span>
                                </a>
                            </div>
                        </div>

                        <!-- Group 3: GENERAL -->
                        <div>
                            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">GENERAL</span>
                            <div class="space-y-1">
                                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-blue-900 hover:bg-slate-50 font-bold text-xs transition-all">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Settings</span>
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- User Profile Footer -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-[#0f2b5c] text-white font-extrabold flex items-center justify-center text-xs shrink-0">
                            A
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-slate-800 truncate">Administrator JPP</p>
                            <p class="text-[10px] text-slate-400 truncate">admin@jim.com</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 md:p-8 overflow-y-auto">
                <!-- Pembungkus Animasi Terpusat untuk Semua Halaman -->
                <div class="animate-page-pop">
                    {{ $slot }}
                </div>
            </main>

        </div>

    </body>

    </html>