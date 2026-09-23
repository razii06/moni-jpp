<x-admin-layout title="Dashboard Overview">
    <div class="w-full min-w-0 space-y-8">
        <!-- CSS Animasi Pop-Up -->
        <style>
            @keyframes popUp {
                0% {
                    opacity: 0;
                    transform: translateY(24px) scale(0.95);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .animate-pop-up {
                animation: popUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
            }
            .delay-100 { animation-delay: 100ms; }
            .delay-200 { animation-delay: 200ms; }
            .delay-300 { animation-delay: 300ms; }
            .delay-400 { animation-delay: 400ms; }
            .delay-500 { animation-delay: 500ms; }
            .delay-600 { animation-delay: 600ms; }
        </style>

        <!-- Header Page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-pop-up">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Dashboard Overview</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Ringkasan performa dan statistik pekerjaan JPP PT Pupuk Iskandar Muda.</p>
            </div>
            <div>
                <a href="{{ route('admin.job-packages.create') }}"
                    class="bg-gradient-to-r from-[#0f2b5c] to-blue-900 hover:from-blue-900 hover:to-[#0f2b5c] text-white px-5 py-3 rounded-xl font-bold text-xs transition-all shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 hover:scale-105">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Job Package</span>
                </a>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- Total Paket (Blue Card) -->
            <div class="animate-pop-up delay-100 bg-white p-6 rounded-2xl border border-blue-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-600"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-extrabold text-blue-900/70 uppercase tracking-wider">Total Paket</span>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-black text-slate-900">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 font-medium mt-1">Keseluruhan proyek terdaftar</p>
            </div>

            <!-- Sedang Berjalan (Amber Card) -->
            <a href="{{ route('admin.job-packages.index') }}#sedang-berjalan" class="block transition hover:scale-[1.01]">
                <div class="animate-pop-up delay-200 bg-white p-6 rounded-2xl border border-amber-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold text-amber-800 uppercase tracking-wider">Sedang Berjalan</span>
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-black text-amber-600">{{ number_format($stats['running'] ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">Progres di antara 1% - 99%</p>
                </div>
            </a>

            <!-- Selesai (Emerald Card) -->
            <a href="{{ route('admin.job-packages.index') }}#selesai" class="block transition hover:scale-[1.01]">
                <div class="animate-pop-up delay-300 bg-white p-6 rounded-2xl border border-emerald-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider">Selesai (100%)</span>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-black text-emerald-600">{{ number_format($stats['completed'] ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">Pekerjaan rampung</p>
                </div>
            </a>

            <!-- Job Package Batal (Slate Card) -->
            <a href="{{ route('admin.job-packages.index') }}#batal" class="block transition hover:scale-[1.01]">
                <div class="animate-pop-up delay-400 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-slate-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold text-slate-600 uppercase tracking-wider">Job Package Batal</span>
                        <div class="p-3 bg-slate-100 text-slate-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-black text-slate-600">{{ number_format($stats['cancelled'] ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">Dibatalkan, tidak dihitung dalam statistik lain</p>
                </div>
            </a>

        </div>

        <!-- Cards Kontrol Target & Risiko -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Pekerjaan Terlambat / Critical Card -->
            <div class="animate-pop-up delay-400 bg-gradient-to-r from-rose-950 via-rose-900 to-slate-900 text-white p-6 rounded-2xl shadow-lg shadow-rose-950/20 flex items-center justify-between border border-rose-800/40">
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Pekerjaan Terlambat / Critical
                    </span>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-white tracking-tight">
                            {{ number_format($targetStats['terlambat'] ?? 0, 0, ',', '.') }}
                        </h3>
                        <span class="text-sm font-semibold text-rose-200">Paket</span>
                    </div>
                    <p class="text-xs text-rose-200/80 font-medium">Melewati target tanggal selesai & progres belum 100%</p>
                </div>
                <div class="p-4 bg-rose-500/20 backdrop-blur-md rounded-2xl border border-rose-500/30 text-rose-400 shrink-0">
                    <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Target Selesai Bulan Ini Card -->
            <div class="animate-pop-up delay-500 bg-gradient-to-r from-teal-950 via-teal-900 to-slate-900 text-white p-6 rounded-2xl shadow-lg shadow-teal-950/20 flex items-center justify-between border border-teal-800/40">
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Target Selesai ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})
                    </span>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-white tracking-tight">
                            {{ number_format($targetStats['selesai_bulan_ini'] ?? 0, 0, ',', '.') }}
                        </h3>
                        <span class="text-sm font-semibold text-teal-200">Paket</span>
                    </div>
                    <p class="text-xs text-teal-200/80 font-medium">Jadwal penyelesaian & closing administrasi bulan ini</p>
                </div>
                <div class="p-4 bg-teal-500/20 backdrop-blur-md rounded-2xl border border-teal-500/30 text-teal-300 shrink-0">
                    <svg class="w-8 h-8 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Line Chart Container dengan Filter Tanggal -->
            <div class="animate-pop-up delay-500 lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm min-w-0">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                            Tren Pembuatan Job Package
                        </h4>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                            Periode: {{ \Carbon\Carbon::parse($startDate ?? now()->subWeeks(8))->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($endDate ?? now())->translatedFormat('d M Y') }}
                        </p>
                    </div>

                    <!-- Form Filter Tanggal & Pengelompokan -->
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
                        <div>
                            <input type="date" name="start_date" value="{{ request('start_date', $startDate ?? '') }}" 
                                class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <span class="text-xs font-bold text-slate-400">s/d</span>
                        <div>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}" 
                                class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>

                        <!-- Mode Grouping: Harian, Mingguan, Bulanan -->
                        <select name="group_by" class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 cursor-pointer">
                            <option value="daily" {{ request('group_by') == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ request('group_by', 'weekly') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ request('group_by') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>

                        <button type="submit" class="px-3 py-1 bg-[#0f2b5c] hover:bg-blue-900 text-white font-bold text-xs rounded-lg transition-all shadow-sm">
                            Filter
                        </button>

                        @if(request()->has('start_date'))
                            <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition-all">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Canvas Grafik -->
                <div class="h-72 relative w-full">
                    <canvas id="jobPackageTrendChart"></canvas>
                </div>
            </div>

            <!-- Doughnut Chart Container -->
            <div class="animate-pop-up delay-600 lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm min-w-0">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Distribusi Status Progres</h4>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>
                <div class="h-72 relative w-full flex items-center justify-center">
                    <canvas id="distributionChart" data-selesai="{{ $distribution['selesai'] ?? 0 }}"
                        data-baik="{{ $distribution['baik'] ?? 0 }}"
                        data-perlu-perhatian="{{ $distribution['perlu_perhatian'] ?? 0 }}"
                        data-belum-mulai="{{ $distribution['belum_mulai'] ?? 0 }}"></canvas>
                </div>
            </div>

        </div>

        <!-- Table Section -->
        <div class="animate-pop-up delay-600 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mt-8 min-w-0">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-black text-slate-800 uppercase tracking-wide">Job Package Terbaru</h2>
                    <p class="text-sm text-slate-500 mt-1">Daftar paket pekerjaan terbaru yang terdaftar di sistem.</p>
                </div>
                <a href="{{ route('admin.job-packages.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#0f2b5c] to-blue-900 text-white font-bold text-xs transition-all duration-300 shadow-md shadow-blue-900/20 hover:shadow-[0_0_20px_rgba(245,158,11,0.65)] hover:border-amber-400 border border-transparent hover:scale-105 whitespace-nowrap">
                    <span>Lihat Semua</span>
                    <span class="text-amber-400 font-extrabold text-sm">&rarr;</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="bg-slate-50/60 text-[11px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-6 py-4 w-[38%]">JOB PACKAGE & SURAT</th>
                            <th class="px-6 py-4 w-[20%]">REFERENSI</th>
                            <th class="px-6 py-4 w-[18%]">JADWAL</th>
                            <th class="px-6 py-4 w-[14%]">STATUS & PROGRES</th>
                            <th class="px-6 py-4 text-center w-[10%]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($latestJobs as $job)
                            @php
                                $progress = max(0, min(100, (float) ($job->hasil_progres ?? 0)));
                                $isCancelled = $job->status === 'batal';
                                
                                // Ambil semua No. Surat / BAK (Mencakup relasi, JSON, string koma, atau newline)
                                $suratDocsList = [];
                                
                                if (isset($job->suratBakDocs) && $job->suratBakDocs->count() > 0) {
                                    foreach ($job->suratBakDocs as $sDoc) {
                                        $val = $sDoc->no_surat ?? $sDoc->no_surat_bak_doc ?? $sDoc->nomor_surat ?? null;
                                        if (!empty($val)) {
                                            $suratDocsList[] = $val;
                                        }
                                    }
                                }
                                
                                if (empty($suratDocsList)) {
                                    $rawSurat = $job->no_surat_bak_doc ?? $job->no_surat ?? null;
                                    if (!empty($rawSurat)) {
                                        if (is_array($rawSurat)) {
                                            $suratDocsList = $rawSurat;
                                        } else {
                                            $decoded = json_decode($rawSurat, true);
                                            if (is_array($decoded)) {
                                                $suratDocsList = $decoded;
                                            } else {
                                                $suratDocsList = preg_split('/[\n\r,]+/', $rawSurat);
                                            }
                                        }
                                    }
                                }
                                
                                $suratDocsList = array_values(array_filter(array_map('trim', (array)$suratDocsList)));

                                // PO
                                $pos = $job->pos;
                                $firstPo = $pos ? $pos->first() : null;
                                $extraPoCount = $pos ? ($pos->count() - 1) : 0;

                                // Helper mengambil nomor PO
                                $getPoNumber = function($poItem) {
                                    if (is_object($poItem)) {
                                        return $poItem->no_po ?? $poItem->po_number ?? $poItem->nomor_po ?? $poItem->no_po_doc ?? '-';
                                    }
                                    if (is_array($poItem)) {
                                        return $poItem['no_po'] ?? $poItem['po_number'] ?? $poItem['nomor_po'] ?? $poItem['no_po_doc'] ?? '-';
                                    }
                                    return is_scalar($poItem) ? (string)$poItem : '-';
                                };

                                // Helper mengambil harga PO
                                $getPoPrice = function($poItem) {
                                    $rawVal = null;

                                    if (is_object($poItem)) {
                                        $rawVal = $poItem->nilai_po ?? $poItem->harga ?? $poItem->nominal ?? $poItem->nilai 
                                            ?? $poItem->total ?? $poItem->harga_po ?? $poItem->nilai_pekerjaan 
                                            ?? $poItem->nilai_kontrak ?? $poItem->amount ?? $poItem->price ?? $poItem->total_harga ?? null;
                                    } elseif (is_array($poItem)) {
                                        $rawVal = $poItem['nilai_po'] ?? $poItem['harga'] ?? $poItem['nominal'] ?? $poItem['nilai'] 
                                            ?? $poItem['total'] ?? $poItem['harga_po'] ?? $poItem['nilai_pekerjaan'] 
                                            ?? $poItem['nilai_kontrak'] ?? $poItem['amount'] ?? $poItem['price'] ?? $poItem['total_harga'] ?? null;
                                    }

                                    if ($rawVal !== null && $rawVal !== '') {
                                        $cleaned = preg_replace('/[^0-9]/', '', (string)$rawVal);
                                        if ($cleaned !== '') {
                                            return 'Rp ' . number_format((float)$cleaned, 0, ',', '.');
                                        }
                                    }
                                    
                                    return null;
                                };

                                // Departemen
                                $deptList = $job->permintaanDaris ? $job->permintaanDaris->pluck('permintaan_dari')->filter()->join(', ') : '';
                                if (empty($deptList)) {
                                    $deptList = $job->departemen ?? '-';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <!-- JOB PACKAGE & SURAT -->
                                <td class="px-6 py-5 align-top">
                                    <a href="{{ route('admin.job-packages.show', $job) }}" class="text-sm font-black text-slate-900 hover:text-blue-700 leading-snug block mb-2">
                                        {{ $job->job_package }}
                                    </a>
                                    
                                    <div class="space-y-2 text-slate-500 font-medium">
                                        <!-- No. Surat / BAK Tampil Vertikal (1 Nomor 1 Baris Tanpa Sembunyi) -->
                                        <div class="flex flex-col gap-1">
                                            <span class="text-slate-400 font-semibold text-xs">No. Surat / BAK:</span>
                                            @if(count($suratDocsList) > 0)
                                                <div class="flex flex-col space-y-1 w-full">
                                                    @foreach($suratDocsList as $sNum)
                                                        <div class="text-xs font-bold text-blue-700 bg-blue-50/80 border border-blue-200/80 px-2.5 py-1 rounded-lg w-fit leading-tight break-all">
                                                            {{ $sNum }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-slate-400 font-semibold text-xs">-</span>
                                            @endif
                                        </div>

                                        <p><span class="text-slate-400">Departemen:</span> <span class="text-slate-700 font-medium">{{ $deptList }}</span></p>
                                        <p><span class="text-slate-400">SO:</span> <span class="text-slate-900 font-bold">{{ $job->no_service_order ?? '-' }}</span></p>
                                    </div>
                                </td>

                                <!-- REFERENSI -->
                                <td class="px-6 py-5 align-top">
                                    <div class="space-y-1 text-slate-500 font-medium text-xs">
                                        <!-- Fitur PO dengan Popover Alpine.js -->
                                        <div class="flex items-center gap-1.5 flex-wrap" x-data="{ openPO: false }">
                                            <span class="text-slate-400 font-medium">No. PO:</span>
                                            
                                            @if($pos && $pos->count() > 0)
                                                <span class="font-bold text-slate-800">{{ $getPoNumber($firstPo) }}</span>

                                                @if($extraPoCount > 0)
                                                    <div class="relative inline-block">
                                                        <button @click="openPO = !openPO" 
                                                                @click.outside="openPO = false"
                                                                type="button"
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors cursor-pointer border border-amber-300">
                                                            +{{ $extraPoCount }} PO
                                                        </button>

                                                        <!-- Popover Daftar PO & Harga -->
                                                        <div x-show="openPO" 
                                                                x-transition:enter="transition ease-out duration-150"
                                                                x-transition:enter-start="opacity-0 scale-95"
                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                x-transition:leave="transition ease-in duration-100"
                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                x-transition:leave-end="opacity-0 scale-95"
                                                                class="absolute left-0 mt-1 w-64 bg-white border border-slate-200 rounded-xl shadow-xl p-3 z-30" 
                                                                style="display: none;">
                                                            
                                                            <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-slate-100">
                                                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Daftar No. PO & Harga</span>
                                                                <span class="text-[10px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded-full font-bold border border-amber-200/60">{{ $pos->count() }} Total</span>
                                                            </div>

                                                            <ul class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                                                @foreach($pos as $poItem)
                                                                    @php
                                                                        $poNum = $getPoNumber($poItem);
                                                                        $poPrice = $getPoPrice($poItem);
                                                                    @endphp
                                                                    <li class="flex items-center justify-between gap-2 text-xs font-semibold text-slate-700 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                                                                        <span class="select-all font-bold text-slate-800">{{ $poNum }}</span>
                                                                        @if($poPrice)
                                                                            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/70 shrink-0">
                                                                                {{ $poPrice }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-[10px] font-medium text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded shrink-0">
                                                                                Tanpa Harga
                                                                            </span>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif(!empty($job->no_po))
                                                <span class="font-bold text-slate-800">{{ $job->no_po }}</span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </div>

                                        <!-- Notif -->
                                        <div>
                                            <span class="text-slate-400">Notif:</span> 
                                            <span class="text-slate-700 font-medium">{{ $job->no_service_notifikasi ?? '-' }}</span>
                                        </div>

                                        <!-- Estimasi OE -->
                                        <div>
                                            <span class="text-slate-400">Estimasi OE:</span> 
                                            <strong class="text-slate-800">Rp {{ number_format($job->owner_estimate ?? 0, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </td>

                                <!-- JADWAL -->
                                <td class="px-6 py-5 align-top whitespace-nowrap">
                                    <div class="space-y-2 text-slate-600 font-medium">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Mulai: <strong class="text-slate-800">{{ optional($job->tanggal_mulai_pekerjaan)->format('d M Y') ?: '-' }}</strong></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Target: <strong class="text-slate-800">{{ optional($job->tanggal_selesai_pekerjaan)->format('d M Y') ?: '-' }}</strong></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- STATUS & PROGRES -->
                                <td class="px-6 py-5 align-top whitespace-nowrap">
                                    <div class="space-y-2.5">
                                        <div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold {{ $isCancelled ? 'bg-rose-50 text-rose-600' : ($progress >= 100 ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600') }}">
                                                {{ $isCancelled ? 'Dibatalkan' : ($progress >= 100 ? 'Selesai 100%' : 'Sedang Berjalan') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="h-2 rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs font-black text-blue-900">{{ number_format($progress, 0) }}%</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- AKSI -->
                                <td class="px-6 py-5 align-top text-center whitespace-nowrap">
                                    <a href="{{ route('admin.job-packages.show', $job) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">
                                    Belum ada data pekerjaan terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- Pagination Bar (Aksen Warna Kuning / Amber) -->
        @if(method_exists($latestJobs, 'hasPages') && $latestJobs->hasPages())
            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-700">{{ $latestJobs->firstItem() }}</span> - <span class="font-bold text-slate-700">{{ $latestJobs->lastItem() }}</span> dari <span class="font-bold text-slate-700">{{ $latestJobs->total() }}</span> data
                </div>
                
                <nav role="navigation" aria-label="Pagination Navigation" class="inline-flex items-center rounded-lg border border-amber-400 bg-white overflow-hidden shadow-sm">
                    {{-- Previous Page Link --}}
                    @if ($latestJobs->onFirstPage())
                        <span class="px-3.5 py-2 text-xs font-bold text-amber-300 cursor-not-allowed bg-slate-50">«</span>
                    @else
                        <a href="{{ $latestJobs->previousPageUrl() }}" class="px-3.5 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 transition-colors">«</a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($latestJobs->getUrlRange(1, $latestJobs->lastPage()) as $page => $url)
                        @if ($page == $latestJobs->currentPage())
                            <span class="px-4 py-2 text-xs font-extrabold bg-amber-500 text-white border-l border-amber-200">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 border-l border-amber-200 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($latestJobs->hasMorePages())
                        <a href="{{ $latestJobs->nextPageUrl() }}" class="px-3.5 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 border-l border-amber-200 transition-colors">»</a>
                    @else
                        <span class="px-3.5 py-2 text-xs font-bold text-amber-300 cursor-not-allowed bg-slate-50 border-l border-amber-200">»</span>
                    @endif
                </nav>
            </div>
        @endif

        <!-- Script Rendering Chart -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') return;

            // 1. Line Chart Tren Pembuatan Job Package
            const elTrend = document.getElementById('jobPackageTrendChart');
            if (elTrend) {
                const ctx = elTrend.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(15, 43, 92, 0.25)');
                gradient.addColorStop(1, 'rgba(15, 43, 92, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels ?? []),
                        datasets: [{
                            label: 'Jumlah Paket',
                            data: @json($dataValues ?? []),
                            borderColor: '#0f2b5c',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#f59e0b',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Doughnut Chart Distribusi Status Progres
            const elDist = document.getElementById('distributionChart');
            if (elDist) {
                const selesai = Number(elDist.dataset.selesai) || 0;
                const baik = Number(elDist.dataset.baik) || 0;
                const perluPerhatian = Number(elDist.dataset.perluPerhatian) || 0;
                const belumMulai = Number(elDist.dataset.belumMulai) || 0;

                new Chart(elDist.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai (≥90%)', 'Baik (50-89%)', 'Perlu Perhatian (<50%)', 'Belum Mulai (0%)'],
                        datasets: [{
                            data: [selesai, baik, perluPerhatian, belumMulai],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#94a3b8'],
                            borderWidth: 3,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        cutout: '72%'
                    }
                });
            }
        });
        </script>
    </div>
</x-admin-layout>