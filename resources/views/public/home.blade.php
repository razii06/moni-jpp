<x-guest-layout>
    <style>
        /* Animasi Pop-Up bertahap saat halaman pertama kali dimuat */
        @keyframes popUpIn {
            0% {
                opacity: 0;
                transform: translateY(28px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-popup {
            opacity: 0;
            animation: popUpIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Delay animasi berurutan */
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
        .delay-600 { animation-delay: 600ms; }
        .delay-700 { animation-delay: 700ms; }

        /* Custom Class Efek Cahaya Kuning */
        .glow-yellow {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glow-yellow:hover {
            border-color: #f59e0b !important;
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.5) !important;
            transform: translateY(-4px);
        }
    </style>

    <!-- Hero Banner (Full-Width, Gradasi Biru ke Putih, Teks Rata Tengah) -->
    <div class="animate-popup delay-100 relative py-14 sm:py-20 overflow-hidden text-center"
            style="background: linear-gradient(180deg, #0f2b5c 0%, #1b4385 65%, #f8fafc 100%);">
        
        <!-- Accent Glow Overlay -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-400/20 via-transparent to-transparent pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col items-center justify-center space-y-6">
            
            <!-- Badge -->
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-amber-300 border border-white/20 shadow-sm backdrop-blur-md">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400/70"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                </span>
                Portal Transparansi Job Package
            </span>

            <!-- Judul Utama -->
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight drop-shadow-md">
                Monitoring Progres Pekerjaan <br class="hidden sm:inline" />
                <span class="text-amber-300">Dept. JPP</span>
            </h2>

            <!-- Deskripsi Singkat -->
            <p class="text-sm sm:text-base text-white font-bold leading-relaxed max-w-2xl mx-auto drop-shadow-sm" style="color: #ffffff;">
                Pantau status real-time, alokasi anggaran (OE), penerbitan PO/SO, dan progres
                pencapaian pekerjaan Dept. JPP secara akurat dan terbuka.
            </p>

            <!-- Tombol Aksi -->
            <div class="pt-2">
                <a href="#table-section"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 hover:bg-amber-300 text-[#0f2b5c] font-extrabold text-xs rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <span>Lihat Daftar Pekerjaan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </div>

        </div>
    </div>

    <!-- Wrapper Konten Utama Halaman -->
    <div class="max-w-[1550px] mx-auto px-4 sm:px-6 lg:px-8 pb-24 space-y-8">

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div
                class="animate-popup delay-200 group bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-600"></div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pekerjaan</p>
                    <h3 class="text-4xl font-black text-[#0f2b5c] tracking-tight">
                        {{ $stats['total'] ?? 0 }}
                    </h3>
                    <p class="text-xs font-semibold text-slate-500">Paket Aktif Terdaftar</p>
                </div>
                <div
                    class="kpi-icon-3d p-3.5 bg-blue-50 text-blue-600 rounded-2xl border border-blue-100 shrink-0 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>

            <div
                class="animate-popup delay-300 group bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Berjalan</p>
                    <h3 class="text-4xl font-black text-amber-500 tracking-tight">
                        {{ $stats['running'] ?? 0 }}
                    </h3>
                    <p class="text-xs font-semibold text-amber-600 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Tahap Pengerjaan</span>
                    </p>
                </div>
                <div
                    class="kpi-icon-3d p-3.5 bg-amber-50 text-amber-600 rounded-2xl border border-amber-100 shrink-0 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div
                class="animate-popup delay-400 group bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai (100%)</p>
                    <h3 class="text-4xl font-black text-emerald-600 tracking-tight">
                        {{ $stats['completed'] ?? 0 }}
                    </h3>
                    <p class="text-xs font-semibold text-emerald-600">Terverifikasi Selesai</p>
                </div>
                <div
                    class="kpi-icon-3d p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 shrink-0 group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div id="table-section"
            class="animate-popup delay-500 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xl space-y-5 scroll-mt-6">

            <!-- Header Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-xl tracking-tight flex items-center gap-2.5">
                        <span class="w-2.5 h-6 rounded-full inline-block" style="background-color: #0f2b5c;"></span>
                        Daftar Job Package Pekerjaan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Menampilkan <span class="font-bold text-slate-700">{{ $jobPackages->total() }}</span> paket
                        pekerjaan aktif
                    </p>
                </div>

                <!-- Form Search & Sort -->
                <form method="GET" action="{{ route('home') }}#table-section"
                    class="w-full sm:w-auto flex flex-col sm:flex-row gap-2.5 items-center">
                    <div class="relative w-full sm:w-64" x-data="{ q: '{{ request('search') }}' }">
                        <input type="text" name="search" x-model="q" placeholder="Cari nama, SO, PO..." class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <button type="submit" x-show="q.length > 0" x-cloak class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-[#0f2b5c] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg></button>
                    </div>
                    <div class="relative w-full sm:w-auto flex items-center gap-2">
                        <select name="sort" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white cursor-pointer transition-all">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Urutkan: Terlama</option>
                            <option value="progress_asc" {{ request('sort') == 'progress_asc' ? 'selected' : '' }}>Progres: Terendah</option>
                            <option value="progress_desc" {{ request('sort') == 'progress_desc' ? 'selected' : '' }}>Progres: Tertinggi</option>
                            <option value="oe_desc" {{ request('sort') == 'oe_desc' ? 'selected' : '' }}>OE: Terbesar</option>
                            <option value="oe_asc" {{ request('sort') == 'oe_asc' ? 'selected' : '' }}>OE: Terkecil</option>
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                        </select>
                        @if(request()->filled('search') || request()->filled('sort'))
                        <a href="{{ route('home') }}#table-section" title="Reset Filter" class="p-2.5 bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 transition-all flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-4 px-5 w-3/12 min-w-[260px]">Job Package (SM01)</th>
                            <th class="py-4 px-4 w-3/12 min-w-[230px]">No. Service Notifikasi & Service Order</th>
                            <th class="py-4 px-4 w-2/12 min-w-[140px]">Owner Estimate</th>
                            <th class="py-4 px-4 w-2/12 min-w-[140px]">No. PO</th>
                            <th class="py-4 px-4 w-2/12 min-w-[160px]">Status & Progres</th>
                            <th class="py-4 px-3 w-1/12 min-w-[60px] text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-600 bg-white">
                        @forelse ($jobPackages as $jp)
                        @php
                            $val = floatval(str_replace(',', '.', $jp->hasil_progres ?? 0));
                            $valClamped = min(max($val, 0), 100);
                            $statusText = $valClamped >= 100 ? 'Selesai' : ($valClamped > 0 ? 'Sedang Berjalan' : 'Belum Mulai');
                            $statusBadge = $valClamped >= 100 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($valClamped > 0 ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200');
                            $barBg = $valClamped >= 100 ? 'bg-emerald-500' : 'bg-blue-600';
                            
                            $judulParts = array_map('trim', explode(',', $jp->job_package));
                            $firstJudul = $judulParts[0];
                            $sisaJudulCount = count($judulParts) - 1;
                        @endphp

                        <tr class="hover:bg-slate-50/70 transition-colors duration-150 align-top">

                            <!-- 1. JOB PACKAGE (SM01) -->
                            <td class="py-5 px-5" x-data="{ openJobModal: false }">
                                <div class="group block">
                                    <a href="{{ route('public.job-packages.show', $jp) }}" class="text-sm font-extrabold text-slate-900 hover:text-blue-700 transition-colors leading-snug inline">
                                        {{ $firstJudul }}
                                    </a>
                                    
                                    @if($sisaJudulCount > 0)
                                        <button @click="openJobModal = true" type="button" class="inline-flex items-center px-1.5 py-0.5 ml-1 rounded text-[10px] font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors focus:outline-none">
                                            +{{ $sisaJudulCount }} lainnya
                                        </button>

                                        <!-- Modal Alpine Rincian Job Package -->
                                        <template x-teleport="body">
                                            <div x-show="openJobModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                                                <div @click.away="openJobModal = false" @keydown.escape.window="openJobModal = false" x-show="openJobModal" x-transition:enter="transition ease-out duration-200" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden text-slate-800">
                                                    <div class="px-5 py-4 text-white flex justify-between items-center" style="background-color: #0f2b5c;">
                                                        <div>
                                                            <h4 class="font-bold text-sm">Rincian Job Package</h4>
                                                            <p class="text-xs text-blue-200 font-sans mt-0.5">Berisi {{ count($judulParts) }} Paket Pekerjaan</p>
                                                        </div>
                                                        <button @click="openJobModal = false" type="button" class="text-white/70 hover:text-white text-lg font-bold px-2 hover:bg-white/10 rounded-lg transition-colors">✕</button>
                                                    </div>
                                                    <div class="p-5 max-h-[60vh] overflow-y-auto">
                                                        <table class="w-full text-left border-collapse text-xs">
                                                            <thead>
                                                                <tr class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                                                                    <th class="py-2 px-3 w-8 text-center"></th>
                                                                    <th class="py-2 px-3">Nama Job Package</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-slate-100">
                                                                @foreach($judulParts as $index => $item)
                                                                <tr class="hover:bg-slate-50">
                                                                    <td class="py-2.5 px-3 text-center font-bold text-slate-400">-</td>
                                                                    <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $item }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="bg-slate-50 px-5 py-3.5 border-t border-slate-100 flex justify-end">
                                                        <button @click="openJobModal = false" type="button" class="px-4 py-1.5 text-white font-bold rounded-lg text-xs transition-colors" style="background-color: #0f2b5c;">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. NO SERVICE NOTIFIKASI & ORDER -->
                            <td class="py-5 px-4">
                                <div class="space-y-2 text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-400 w-28 shrink-0">Notifikasi</span> 
                                        <span class="font-mono text-slate-600">{{ $jp->no_service_notifikasi ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-400 w-28 shrink-0">Service Order</span> 
                                        <span class="font-mono font-semibold text-slate-700">{{ $jp->no_service_order ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- 3. OWNER ESTIMATE -->
                            <td class="py-5 px-4 whitespace-nowrap">
                                <span class="font-bold font-mono text-slate-800">
                                    Rp {{ number_format($jp->owner_estimate ?? 0, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- 4. NO. PO -->
                            <td class="py-5 px-4" x-data="{ openPoModal: false }">
                                <div class="text-xs">
                                    @if(isset($jp->pos) && $jp->pos->count() > 0)
                                        <div class="flex flex-wrap items-center gap-1">
                                            <button @click="openPoModal = true" type="button" class="font-bold text-blue-900 hover:text-blue-700 hover:underline text-left">
                                                {{ $jp->pos->first()->po_number ?? $jp->pos->first()->no_po }}
                                            </button>
                                            @if($jp->pos->count() > 1)
                                                <button @click="openPoModal = true" type="button" class="bg-amber-100 text-amber-800 border border-amber-300/80 text-[10px] font-bold px-1.5 py-0.5 rounded-md hover:bg-amber-200 transition-colors">
                                                    +{{ $jp->pos->count() - 1 }} PO
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Modal Alpine PO -->
                                        <template x-teleport="body">
                                            <div x-show="openPoModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                                                <div @click.away="openPoModal = false" x-show="openPoModal" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden text-slate-800">
                                                    <div class="px-5 py-4 text-white flex justify-between items-center" style="background-color: #0f2b5c;">
                                                        <div><h4 class="font-bold text-sm">Rincian PO</h4></div>
                                                        <button @click="openPoModal = false" type="button" class="text-white/70 hover:text-white font-bold px-2 rounded-lg transition-colors">✕</button>
                                                    </div>
                                                    <div class="p-5 overflow-x-auto">
                                                        <table class="w-full text-left border-collapse text-xs">
                                                            <thead>
                                                                <tr class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                                                                    <th class="py-2 px-3 w-8 text-center"></th>
                                                                    <th class="py-2 px-3">No. PO</th>
                                                                    <th class="py-2 px-3 text-right">Nilai PO</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-slate-100">
                                                                @foreach($jp->pos as $index => $po)
                                                                <tr class="hover:bg-slate-50">
                                                                    <td class="py-2.5 px-3 text-center font-bold text-slate-400">-</td>
                                                                    <td class="py-2.5 px-3 font-mono font-semibold text-amber-800">{{ $po->po_number ?? $po->no_po }}</td>
                                                                    <td class="py-2.5 px-3 text-right font-bold font-mono">Rp {{ number_format($po->price ?? $po->harga ?? 0, 0, ',', '.') }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    @elseif($jp->no_po)
                                        <span class="font-bold text-slate-800 font-mono">{{ $jp->no_po }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 5. STATUS & PROGRES -->
                            <td class="py-5 px-4">
                                <div class="flex flex-col gap-2.5 max-w-[150px]">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-extrabold border w-fit {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/80">
                                            <div class="h-full rounded-full {{ $barBg }} transition-all duration-700" style="width: {{ $valClamped }}%"></div>
                                        </div>
                                        <span class="text-xs font-black text-blue-700 shrink-0 font-mono">
                                            {{ number_format($valClamped, 0) }}%
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- 6. TOMBOL DETAIL (MATA) -->
                            <td class="py-5 px-3 text-center align-middle">
                                <a href="{{ route('public.job-packages.show', $jp) }}" 
                                title="Lihat Detail Job Package"
                                class="inline-flex items-center justify-center p-2 rounded-xl text-blue-600 bg-blue-50 border border-blue-300 shadow-[0_0_10px_rgba(37,99,235,0.35)] hover:shadow-[0_0_20px_rgba(37,99,235,0.75)] hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300 group">
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 px-4 text-center bg-slate-50/50">
                                <p class="text-sm font-bold text-slate-500">Data Pekerjaan Tidak Ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Aksen Bingkai Kuning / Amber) -->
            @if(method_exists($jobPackages, 'total') && $jobPackages->total() > 0)
                <div class="pt-4 mt-2 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <span class="font-bold text-slate-700">{{ $jobPackages->firstItem() }}</span> - <span class="font-bold text-slate-700">{{ $jobPackages->lastItem() }}</span> dari <span class="font-bold text-slate-700">{{ $jobPackages->total() }}</span> paket pekerjaan
                    </div>
                    
                    @if($jobPackages->hasPages())
                        <nav role="navigation" aria-label="Pagination Navigation" class="inline-flex items-center rounded-xl border border-amber-400 bg-white overflow-hidden shadow-sm divide-x divide-amber-200">
                            @if ($jobPackages->onFirstPage())
                                <span class="px-3.5 py-2 text-xs font-bold text-amber-300 bg-slate-50 cursor-not-allowed">«</span>
                            @else
                                <a href="{{ $jobPackages->previousPageUrl() }}#table-section" class="px-3.5 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 transition-colors">«</a>
                            @endif

                            @foreach ($jobPackages->getUrlRange(1, $jobPackages->lastPage()) as $page => $url)
                                @if ($page == $jobPackages->currentPage())
                                    <span class="px-4 py-2 text-xs font-extrabold bg-amber-500 text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}#table-section" class="px-4 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($jobPackages->hasMorePages())
                                <a href="{{ $jobPackages->nextPageUrl() }}#table-section" class="px-3.5 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 transition-colors">»</a>
                            @else
                                <span class="px-3.5 py-2 text-xs font-bold text-amber-300 bg-slate-50 cursor-not-allowed">»</span>
                            @endif
                        </nav>
                    @endif
                </div>
            @endif
        </div>

        <!-- SECTION DOKUMENTASI BERANDA -->
        <div class="animate-popup delay-600 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xl space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-xl tracking-tight flex items-center gap-2.5">
                        <span class="w-2.5 h-6 rounded-full inline-block" style="background-color: #0f2b5c;"></span>
                        Dokumentasi Beranda
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Geser ke kanan atau kiri untuk melihat media & dokumen kegiatan
                    </p>
                </div>
                <span class="text-xs font-bold px-3 py-1.5 bg-slate-100 text-slate-600 rounded-xl border border-slate-200/60 w-fit">
                    {{ count($galeri) }} Dokumen & Media
                </span>
            </div>

            <!-- CONTAINER SINGLE ROW -->
            <div class="flex items-center gap-4 overflow-x-auto pb-4 pt-1 snap-x snap-mandatory scrollbar-none focus:outline-none">
                @forelse($galeri as $item)
                    @php
                        $rawPath = $item->file_path;
                        $previewUrl = $rawPath;
                        $fileUrl = $rawPath;

                        if (\Illuminate\Support\Str::startsWith($rawPath, ['http://', 'https://'])) {
                            if (\Illuminate\Support\Str::contains($rawPath, ['drive.google.com', 'googleusercontent.com'])) {
                                preg_match('/[-\w]{25,}/', $rawPath, $matches);
                                $fileId = $matches[0] ?? null;
                                if ($fileId) {
                                    $previewUrl = "https://lh3.googleusercontent.com/d/{$fileId}";
                                    $fileUrl = "https://drive.google.com/file/d/{$fileId}/view";
                                }
                            } 
                            elseif (\Illuminate\Support\Str::contains($rawPath, ['youtube.com', 'youtu.be'])) {
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $rawPath, $ytMatches);
                                $ytId = $ytMatches[1] ?? null;
                                if ($ytId) {
                                    $previewUrl = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
                                }
                            }
                        } else {
                            $previewUrl = \Illuminate\Support\Str::startsWith($rawPath, 'storage/') 
                                ? asset($rawPath) 
                                : asset('storage/' . ltrim($rawPath, '/'));
                            $fileUrl = $previewUrl;
                        }
                    @endphp

                    <div class="w-64 sm:w-72 shrink-0 snap-start rounded-2xl border border-slate-200/80 bg-white p-3 shadow-sm hover:shadow-md transition group glow-yellow">
                        <div class="relative h-40 w-full overflow-hidden rounded-xl bg-slate-100 border border-slate-100 flex items-center justify-center">
                            @if($item->kategori === 'Foto Kegiatan' || \Illuminate\Support\Str::contains($previewUrl, ['lh3.googleusercontent.com', 'img.youtube.com', 'storage/']))
                                <img src="{{ $previewUrl }}" 
                                    alt="{{ $item->judul }}" 
                                    loading="lazy" 
                                    class="h-full w-full object-cover group-hover:scale-105 transition duration-300"
                                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'flex flex-col items-center justify-center text-slate-400\'><svg class=\'w-8 h-8 mb-1\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg><span class=\'text-[10px] font-semibold\'>Gambar Tidak Ditemukan</span></div>';">
                            @elseif($item->kategori === 'Dokumen PDF')
                                <div class="flex flex-col items-center justify-center text-rose-500">
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V7.5L14.5 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-[10px] font-black tracking-wider mt-1">DOKUMEN PDF</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center text-indigo-600">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 border border-indigo-100">
                                        <svg class="h-6 w-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                    <span class="text-[10px] font-bold mt-2">TAUTAN VIDEO</span>
                                </div>
                            @endif

                            <span class="absolute top-2 left-2 rounded-lg bg-slate-900/70 backdrop-blur-md px-2.5 py-1 text-[10px] font-bold text-white">
                                {{ $item->kategori ?? 'Dokumentasi' }}
                            </span>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-2">
                            <div class="overflow-hidden">
                                <h3 class="text-xs font-bold text-slate-800 truncate" title="{{ $item->judul }}">{{ $item->judul }}</h3>
                                <p class="text-[10px] font-medium text-slate-400">{{ $item->created_at?->format('d M Y') }}</p>
                            </div>
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="shrink-0 rounded-xl bg-slate-100 p-2 text-slate-600 hover:bg-[#0f2b5c] hover:text-white transition" title="Buka File">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-2xl">
                        Belum ada dokumentasi beranda yang diunggah.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Info Section -->
        <section class="animate-popup delay-700 info-section grid grid-cols-1 md:grid-cols-3 gap-5 mt-6 mb-20" 
                aria-label="Informasi JPP" 
                style="margin-bottom: 80px;">
            
            <article class="group glow-yellow relative bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between min-h-[176px]">
                <div>
                    <h4 class="flex items-center gap-3 text-slate-900 text-base font-extrabold mb-2">
                        <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 group-hover:bg-amber-400 group-hover:text-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        Penjelasan Sistem
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Pelajari cara kerja sistem monitoring Job Package (SM01) Jasa Pelayanan Pabrik.
                    </p>
                </div>
                <a href="#table-section" class="text-xs font-bold text-blue-900 hover:text-amber-600 inline-flex items-center gap-1">
                    Pelajari Alur Kerja →
                </a>
            </article>

            <article class="group glow-yellow relative bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between min-h-[176px]">
                <div>
                    <h4 class="flex items-center gap-3 text-slate-900 text-base font-extrabold mb-2">
                        <div class="p-2 bg-red-50 text-red-600 rounded-xl border border-red-100 group-hover:bg-amber-400 group-hover:text-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Dokumentasi Umum
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Akses dokumentasi aktivitas dan rekam jejak pekerjaan yang bersifat publik.
                    </p>
                </div>
                <a href="https://www.youtube.com/@sbu.jpp_pim" target="_blank" rel="noopener" class="text-xs font-bold text-blue-900 hover:text-amber-600 inline-flex items-center gap-1">
                    Tonton di YouTube →
                </a>
            </article>

            <article class="group glow-yellow relative p-6 rounded-2xl border border-slate-800 flex flex-col justify-between min-h-[176px] text-white" style="background: linear-gradient(135deg, #0f2b5c, #0b1120);">
                <div>
                    <h4 class="flex items-center gap-3 text-white text-base font-extrabold mb-2">
                        <div class="p-2 bg-white/10 text-white rounded-xl border border-white/20 group-hover:bg-amber-400 group-hover:text-slate-900 transition-colors">
                            <svg class="w-5 h-5 text-white group-hover:text-slate-900 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 01-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        Informasi JPP
                    </h4>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Kunjungi portal utama Jasa Pelayanan Pabrik untuk melihat profil dan layanan kami.
                    </p>
                </div>
                <a href="https://dev.pim.co.id/jpp/" target="_blank" rel="noopener" class="text-xs font-bold text-amber-300 hover:text-amber-200 inline-flex items-center gap-1">
                    Kunjungi Portal JPP ↗
                </a>
            </article>

        </section>

    </div>
</x-guest-layout>