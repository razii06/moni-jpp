<x-guest-layout title="Detail Job Package">
    @php
        $progress = max(0, min(100, (float) $jobPackage->hasil_progres));
        $status = $progress >= 100 ? 'Selesai' : ($progress > 0 ? 'Sedang Berjalan' : 'Belum Mulai');
        $statusClass = $progress >= 100 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : ($progress > 0 ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-slate-600 bg-slate-100 border-slate-200');
    @endphp

    <style>
        .public-detail { animation: detailIn .4s cubic-bezier(.22,1,.36,1) both; }
        .public-detail .detail-panel { border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15,23,42,.03); }
        .public-detail .detail-value { overflow-wrap: anywhere; white-space: pre-line; line-height: 1.5; }
        @keyframes detailIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="public-detail mx-auto max-w-7xl px-4 py-4 sm:px-6 sm:py-6 lg:px-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <!-- Button Kembali ke Monitoring (Warna Biru Mencolok) -->
                <a href="{{ route('home') }}#table-section" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-500/20 transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke monitoring
                </a>
                <p class="mt-3 text-[11px] font-bold uppercase tracking-[.15em] text-blue-600">Detail Job Package</p>
                <h1 class="mt-0.5 text-lg font-black tracking-tight text-slate-900 sm:text-2xl leading-snug">{{ $jobPackage->job_package }}</h1>
            </div>
            <span class="inline-flex shrink-0 w-fit items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-black {{ $statusClass }}">
                {{ $status }} &bull; {{ number_format($progress, 1) }}%
            </span>
        </div>

        <!-- Grid 1: Informasi Dasar -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            
            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wide text-slate-600">Periode</h3>
                </div>
                <p class="text-sm sm:text-base font-bold text-slate-800">{{ $jobPackage->periode ?: '-' }}</p>
            </div>

            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wide text-slate-600">Nomor Dokumen</h3>
                </div>
                <p class="text-sm font-bold text-slate-800">
                    @if($jobPackage->suratBakDocs && $jobPackage->suratBakDocs->count() > 0)
                        {{ $jobPackage->suratBakDocs->pluck('no_surat_bak_doc')->join(', ') }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"></path></svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wide text-slate-600">Departemen</h3>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @if($jobPackage->permintaanDaris && $jobPackage->permintaanDaris->count() > 0)
                        @foreach($jobPackage->permintaanDaris as $permintaan)
                            <span class="inline-block rounded-lg bg-blue-50 px-3 py-1 text-xs font-extrabold text-blue-700">{{ $permintaan->permintaan_dari }}</span>
                        @endforeach
                    @else
                        <p class="text-sm font-bold text-slate-800">-</p>
                    @endif
                </div>
            </div>

            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wide text-slate-600">Tanggal Surat Masuk</h3>
                </div>
                <p class="text-sm sm:text-base font-bold text-slate-800">{{ optional($jobPackage->tanggal_surat_masuk)->format('d M Y') ?: '-' }}</p>
            </div>
            
            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wide text-slate-600">No. Service Notifikasi</h3>
                </div>
                <p class="font-mono text-sm sm:text-base font-extrabold text-slate-800">{{ $jobPackage->no_service_notifikasi ?: '-' }}</p>
            </div>

            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 font-extrabold text-lg">#</div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wide text-slate-600">No. Service Order (SO)</h3>
                </div>
                <p class="font-mono text-sm sm:text-base font-extrabold text-blue-900">{{ $jobPackage->no_service_order ?: '-' }}</p>
            </div>
        </div>

        <!-- Rincian Purchase Order -->
        <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="flex items-center gap-2.5 text-base sm:text-lg font-black text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </span>
                    Rincian Purchase Order
                </h2>
                <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $jobPackage->pos ? $jobPackage->pos->count() : 0 }} Item</span>
            </div>
            
            @php $totalPO = 0; @endphp
            <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1.5">
                @forelse($jobPackage->pos as $po)
                    @php $totalPO += (float) ($po->price ?? $po->harga ?? 0); @endphp
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $po->description ?: 'PO Material' }}</p>
                                <p class="font-mono text-[11px] text-slate-500">{{ $po->no_po ?? $po->po_number }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-mono text-sm font-bold text-slate-800">Rp {{ number_format($po->price ?? $po->harga ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs italic text-slate-400 pb-2">Belum ada data Purchase Order terlampir.</p>
                @endforelse
            </div>
            
            <!-- Total Harga PO -->
            <div class="mt-4 border-t border-slate-200 pt-4 flex justify-end">
                <div class="text-right">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Total Harga</span>
                    <p class="text-xl font-black text-emerald-700 font-mono">Rp {{ number_format($totalPO, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Jadwal Pekerjaan & Detail Progress -->
        <div class="grid gap-4 lg:grid-cols-3 items-stretch">
            <!-- Kiri: Jadwal Pekerjaan -->
            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 flex flex-col justify-between">
                <div>
                    <h2 class="mb-4 flex items-center gap-2.5 text-base sm:text-lg font-black text-slate-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Jadwal Pekerjaan
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-500">Mulai Pekerjaan</span>
                            <span class="text-sm font-bold text-slate-800">{{ optional($jobPackage->tanggal_mulai_pekerjaan)->format('d M Y') ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-1">
                            <span class="text-xs font-semibold text-slate-500">Selesai Pekerjaan</span>
                            <span class="text-sm font-bold text-slate-800">{{ optional($jobPackage->tanggal_selesai_pekerjaan)->format('d M Y') ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Detail Progress -->
            <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5 lg:col-span-2">
                <h2 class="mb-4 flex items-center gap-2.5 text-base sm:text-lg font-black text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </span>
                    Detail Progress
                </h2>
                
                <!-- Total Akumulasi -->
                <div class="mb-5 rounded-xl bg-slate-900 p-4 text-white shadow-sm">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Total Akumulasi Bobot</span>
                        <span class="text-xl font-black text-blue-400">{{ number_format($progress, 1) }}%</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-800">
                        <div class="h-full rounded-full bg-blue-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="mt-2 text-[10px] font-medium text-slate-400 uppercase tracking-wide">
                        RAB (5%) + PBJ (5%) + Fisik (85%) + ADM (5%)
                    </div>
                </div>

                <!-- Sub Item Rincian Bobot -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- RAB -->
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 transition-all hover:bg-slate-50">
                        <div class="mb-2 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700">RAB LP-002 <span class="font-normal text-slate-400">(5%)</span></span>
                            <span class="font-mono text-sm font-black text-blue-600">{{ number_format((float) $jobPackage->rab_lp002, 0) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-blue-500" style="width: {{ (float) $jobPackage->rab_lp002 }}%"></div>
                        </div>
                    </div>

                    <!-- PBJ -->
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 transition-all hover:bg-slate-50">
                        <div class="mb-2 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700">PBJ LP-002 <span class="font-normal text-slate-400">(5%)</span></span>
                            <span class="font-mono text-sm font-black text-amber-600">{{ number_format((float) $jobPackage->pbj_lp002, 0) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ (float) $jobPackage->pbj_lp002 }}%"></div>
                        </div>
                    </div>

                    <!-- Fisik Pekerjaan -->
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 transition-all hover:bg-slate-50">
                        <div class="mb-2 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700">Fisik Pekerjaan <span class="font-normal text-slate-400">(85%)</span></span>
                            <span class="font-mono text-sm font-black text-blue-600">{{ number_format((float) $jobPackage->progress_pekerjaan, 0) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-blue-600" style="width: {{ (float) $jobPackage->progress_pekerjaan }}%"></div>
                        </div>
                    </div>

                    <!-- Progress Dok. Keuangan -->
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 transition-all hover:bg-slate-50">
                        <div class="mb-2 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700">Progress Dok. Keuangan <span class="font-normal text-slate-400">(5%)</span></span>
                            <span class="font-mono text-sm font-black text-amber-600">{{ number_format((float) $jobPackage->proses_adm_keuangan, 0) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ (float) $jobPackage->proses_adm_keuangan }}%"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Aktivitas Terkini -->
        <div class="detail-panel rounded-2xl bg-white p-4 sm:p-5">
            <h2 class="mb-3 flex items-center gap-2.5 text-base sm:text-lg font-black text-slate-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                Aktivitas Terkini
            </h2>
            <div class="detail-value rounded-xl bg-blue-50/50 p-4 text-sm text-slate-700 font-medium">
                {{ $jobPackage->latest_activity ?: 'Belum ada catatan aktivitas terkini.' }}
            </div>
        </div>
        
    </div>
</x-guest-layout>