<x-admin-layout title="Detail Job Package">

    <style>
        .job-detail-page { animation: detailPageIn .55s cubic-bezier(.22,1,.36,1) both; }
        .job-detail-page .detail-copy { overflow-wrap: anywhere; white-space: pre-line; line-height: 1.65; }
        .job-detail-page .detail-panel { border-color: #e5ebf3; box-shadow: 0 7px 24px rgba(15,23,42,.045); }
        .job-detail-page .detail-action { transition: transform .2s ease, box-shadow .2s ease; }
        .job-detail-page .detail-action:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(15,23,42,.10); }
        @keyframes detailPageIn { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @media (prefers-reduced-motion: reduce) { .job-detail-page { animation: none; } }
    </style>

    @php
        // Perhitungan Progres Utama
        $progress = isset($jobPackage->hasil_progres) 
            ? max(0, min(100, (float) $jobPackage->hasil_progres))
            : (($jobPackage->rab_lp002 * 0.05) + ($jobPackage->pbj_lp002 * 0.05) + ($jobPackage->progress_pekerjaan * 0.85) + ($jobPackage->proses_adm_keuangan * 0.05));

        // Penentuan Status
        $isCancelled = ($jobPackage->status === 'batal');
        $tglSelesai = $jobPackage->tanggal_selesai_pekerjaan ? \Carbon\Carbon::parse($jobPackage->tanggal_selesai_pekerjaan)->startOfDay() : null;
        $isLate = $tglSelesai && now()->startOfDay()->gt($tglSelesai) && $progress < 100;

        $statusBadge = match(true) {
            $isCancelled => ['label' => 'Batal / Belum Ada Tindak Lanjut', 'color' => 'slate'],
            $isLate => ['label' => 'Terlambat', 'color' => 'rose'],
            $progress >= 100 => ['label' => 'Selesai', 'color' => 'emerald'],
            $progress > 0 => ['label' => 'Sedang Berjalan', 'color' => 'blue'],
            default => ['label' => 'Belum Mulai', 'color' => 'amber'],
        };

        // Estimasi Sisa Hari
        $sisaHari = $tglSelesai ? (int) round(now()->startOfDay()->diffInDays($tglSelesai, false)) : null;

        // Perhitungan Finansial & KPI
        $oe = (float) ($jobPackage->owner_estimate ?? 0);
        $finalHarga = (float) ($jobPackage->final_harga ?? 0);
        $selisihBudget = $oe - $finalHarga;
        $persenEfisiensi = $oe > 0 ? ($selisihBudget / $oe) * 100 : 0;

        // Daftar dokumen wajib/utama (Bagian C)
        $docFields = [
            'doc_surat_permintaan'  => 'Surat Permintaan',
            'doc_surat_izin_prinsip' => 'Surat Izin Prinsip',
            'doc_bak'               => 'BAK / Negosiasi',
            'doc_rab'               => 'RAB',
            'doc_form_pbj'          => 'Form Permintaan Barang & Jasa',
            'doc_tor'               => 'TOR',
            'doc_bast'              => 'BAST',
            'doc_laporan_pekerjaan' => 'Laporan Pekerjaan',
        ];

        // Kategori tambahan (Bagian D)
        $docPendukung = [
            'doc_pendukung_lainnya' => 'Dokumen Pendukung Lainnya'
        ];

        // Penggabungan untuk total kelengkapan dokumen
        $allDocFields = array_merge($docFields, $docPendukung);
        $docFilled = collect(array_keys($allDocFields))->filter(fn($f) => !empty($jobPackage->$f))->count();
        $docTotal = count($allDocFields);

        // Koleksi PO & Riwayat Aktivitas
        $posList = $jobPackage->pos ?? collect([]);
        $logsList = $jobPackage->activityLogs ?? $jobPackage->activities ?? collect([]);
    @endphp

    <div class="job-detail-page space-y-6 w-full max-w-7xl mx-auto pb-12">

        <!-- Banner Pemberitahuan Status Batal -->
        @if ($isCancelled)
            <div class="bg-slate-100 border border-slate-200 text-slate-600 px-4 py-3 rounded-2xl text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-medium">Job Package ini berstatus <strong>Batal</strong>. Data tetap tersimpan lengkap dan dapat diaktifkan kembali kapan saja.</span>
            </div>
        @endif

        <!-- Header Utama & Tombol Aksi -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <a href="{{ route('admin.job-packages.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-blue-900 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar
                </a>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wide bg-{{ $statusBadge['color'] }}-100 text-{{ $statusBadge['color'] }}-800 border border-{{ $statusBadge['color'] }}-200">
                        {{ $statusBadge['label'] }}
                    </span>
                    <span class="text-xs text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-500">ID #{{ $jobPackage->id }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight leading-snug max-w-3xl {{ $isCancelled ? 'text-slate-400' : '' }}">
                    {{ $jobPackage->job_package ?? $jobPackage->nama_pekerjaan ?? 'Detail Job Package' }}
                </h1>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                @if (Route::has('admin.job-packages.reactivate') && Route::has('admin.job-packages.cancel'))
                    @if ($isCancelled)
                        <form action="{{ route('admin.job-packages.reactivate', $jobPackage) }}" method="POST" onsubmit="return confirm('Aktifkan kembali Job Package ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-emerald-600/20 transition-all detail-action flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Aktifkan Kembali
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.job-packages.cancel', $jobPackage) }}" method="POST" onsubmit="return confirm('Batalkan Job Package ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 px-4 py-2.5 rounded-xl font-bold text-xs transition-all detail-action flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Batalkan
                            </button>
                        </form>
                    @endif
                @endif

                <a href="{{ route('admin.job-packages.edit', $jobPackage) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/20 transition-all detail-action">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Data
                </a>
            </div>
        </div>

        <!-- Hero Section & Ringkasan Stats KPI -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <!-- Card Progres Utama -->
            <div class="lg:col-span-4 bg-gradient-to-br from-blue-900 via-blue-950 to-slate-900 text-white p-6 rounded-2xl shadow-md relative overflow-hidden flex flex-col justify-between {{ $isCancelled ? 'grayscale opacity-75' : '' }}">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-extrabold text-blue-200 uppercase tracking-wider">Progres Pekerjaan</span>
                        <span class="text-[10px] bg-blue-800/60 border border-blue-700 px-2 py-0.5 rounded-full text-blue-100">
                            {{ $jobPackage->creator->name ?? 'Administrator JPP' }}
                        </span>
                    </div>
                    <div class="text-5xl font-black mt-2 tracking-tight">{{ number_format($progress, 1) }}%</div>
                </div>
                <div class="mt-6">
                    <div class="w-full bg-blue-950/80 rounded-full h-2.5 overflow-hidden border border-blue-800/50">
                        <div class="bg-amber-400 h-2.5 rounded-full transition-all duration-500 shadow-[0_0_8px_rgba(251,191,36,0.6)]" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Stats Metrics KPI -->
            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                <!-- Anggaran & Efisiensi -->
                <div class="flex flex-col justify-between space-y-4 pt-3 sm:pt-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Anggaran & Efisiensi</span>
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 mb-3 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black {{ $selisihBudget >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ number_format(abs($persenEfisiensi), 1, ',', '.') }}%
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium mt-1 block">
                            {{ $selisihBudget >= 0 ? 'Lebih hemat dari OE' : 'Melebihi Owner Estimate' }}
                        </span>
                    </div>
                </div>

                <!-- Tenggat Jadwal -->
                <div class="flex flex-col justify-between space-y-4 pt-4 sm:pt-0 sm:pl-6">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tenggat Jadwal</span>
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 mb-3 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @if($sisaHari === null)
                            <div class="text-3xl font-black text-slate-300">-</div>
                            <span class="text-[11px] text-slate-400 font-medium mt-1 block">Tanggal selesai belum diisi</span>
                        @elseif($isLate)
                            <div class="text-3xl font-black text-rose-600">{{ abs($sisaHari) }} Hari</div>
                            <span class="text-[11px] text-rose-500 font-semibold mt-1 block">Telah melewati tenggat</span>
                        @elseif($progress >= 100)
                            <div class="text-2xl font-black text-emerald-600">Selesai Tuntas</div>
                            <span class="text-[11px] text-slate-400 font-medium mt-1 block">Pekerjaan rampung</span>
                        @else
                            <div class="text-3xl font-black text-slate-800">{{ abs($sisaHari) }} Hari</div>
                            <span class="text-[11px] text-slate-400 font-medium mt-1 block">Tersisa hingga tenggat</span>
                        @endif
                    </div>
                </div>

                <!-- Dokumen Pendukung -->
                <div class="flex flex-col justify-between space-y-4 pt-4 sm:pt-0 sm:pl-6">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Dokumen Pendukung</span>
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 mb-3 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-slate-900">
                            {{ $docFilled }} <span class="text-slate-300 font-normal text-xl">/ {{ $docTotal }}</span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium mt-1 block">Berkas terlampir sistem</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BARIS 1: Informasi Utama & Rincian Purchase Order (PO) (Simetris 50:50) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            
            <!-- Kiri: Grid Kartu Informasi Utama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- 1. No. Dokumen (Diubah judul & bullet format ke -) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">No. Dokumen</span>
                    </div>
                    <div class="text-xs font-bold text-slate-800 break-all pl-0.5">
                        @if($jobPackage->suratBakDocs && $jobPackage->suratBakDocs->isNotEmpty())
                            <div class="space-y-1">
                                @foreach($jobPackage->suratBakDocs as $surat)
                                    <span class="block">
                                        - {{ $surat->no_surat_bak_doc }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            {{ $jobPackage->no_surat ?? $jobPackage->no_surat_bak_doc ?? '-' }}
                        @endif
                    </div>
                </div>

                <!-- 2. Tanggal Surat Masuk -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Tanggal Surat Masuk</span>
                    </div>
                    <div class="text-xs font-black text-slate-800 pl-0.5">
                        {{ $jobPackage->tanggal_surat_masuk ? \Carbon\Carbon::parse($jobPackage->tanggal_surat_masuk)->format('d M Y') : '-' }}
                    </div>
                </div>

                <!-- 3. Permintaan Dari / Departemen -->
                <div class="sm:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Permintaan Dari / Departemen</span>
                    </div>
                    <div class="text-xs pl-0.5">
                        @if($jobPackage->permintaanDaris && $jobPackage->permintaanDaris->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($jobPackage->permintaanDaris as $permintaan)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $permintaan->permintaan_dari }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="font-extrabold text-slate-800">
                                {{ $jobPackage->departemen ?? '-' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- 4. No. Service Order (SO) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">No. Service Order (SO)</span>
                    </div>
                    <div class="text-xs font-bold text-blue-900 font-mono pl-0.5">
                        {{ $jobPackage->no_service_order ?? '-' }}
                    </div>
                </div>

                <!-- 5. No. Service Notifikasi -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">No. Service Notifikasi</span>
                    </div>
                    <div class="text-xs font-bold text-slate-800 font-mono pl-0.5">
                        {{ $jobPackage->no_service_notifikasi ?? '-' }}
                    </div>
                </div>

                <!-- 6. Owner Estimate (OE) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Owner Estimate (OE)</span>
                    </div>
                    <div class="text-sm font-black text-slate-900 pl-0.5">
                        Rp {{ number_format($oe, 0, ',', '.') }}
                    </div>
                </div>

                <!-- 7. Final Harga -->
                <div class="bg-emerald-50/60 rounded-2xl border border-emerald-200/80 shadow-sm p-4 flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider">Final Harga</span>
                    </div>
                    <div class="text-sm font-black text-emerald-600 pl-0.5">
                        Rp {{ number_format($finalHarga, 0, ',', '.') }}
                    </div>
                </div>

            </div>

            <!-- Kanan: Rincian Purchase Order (PO) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between space-y-4">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/>
                            </svg>
                            Rincian Purchase Order
                        </h2>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold">
                            {{ count($posList) }} Item
                        </span>
                    </div>

                    <div class="space-y-2.5 max-h-[320px] overflow-y-auto pr-1">
                        @forelse($posList as $index => $po)
                            @php
                                $namaPo = $po->nama_po ?? $po->description ?? 'PO Item';
                                $nilaiPo = (float) ($po->nilai_po ?? $po->price ?? 0);
                            @endphp
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-blue-900 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="truncate">
                                        <span class="font-bold text-slate-800 block truncate">{{ $namaPo }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $po->no_po ?? '-' }}</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-emerald-600 shrink-0 tabular-nums">
                                    Rp {{ number_format($nilaiPo, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs font-medium">
                                Belum ada Rincian Purchase Order.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-black text-slate-900">
                    <span>Total Keseluruhan PO</span>
                    <span class="text-emerald-600 text-base">
                        Rp {{ number_format($finalHarga > 0 ? $finalHarga : $posList->sum(fn($p) => $p->nilai_po ?? $p->price ?? 0), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- BARIS 2: Bobot & Detail Progres (KIRI) & Jadwal Pekerjaan (KANAN) (Simetris 50:50) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            
            <!-- Kiri: Bobot Pekerjaan (Warna diganti Kuning & Biru) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Bobot & Detail Progres
                        </span>
                        <span class="text-xs font-bold text-blue-900 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">
                            Total: {{ number_format($progress, 1) }}%
                        </span>
                    </h2>

                    <!-- Ringkasan Total Keseluruhan Bobot -->
                    <div class="p-3.5 bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-xl shadow-sm space-y-2">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-xs text-blue-100 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Total Akumulasi Bobot
                            </span>
                            <span class="text-base font-black text-amber-400">{{ number_format($progress, 1) }}%</span>
                        </div>
                        <div class="w-full bg-blue-950/80 rounded-full h-2 overflow-hidden border border-blue-700/50">
                            <div class="bg-amber-400 h-2 rounded-full transition-all duration-500 shadow-[0_0_8px_rgba(251,191,36,0.5)]" style="width: {{ min(100, $progress) }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-blue-200 font-medium">
                            <span>RAB (5%) + PBJ (5%) + Fisik (85%) + ADM (5%)</span>
                            <span>Target: 100%</span>
                        </div>
                    </div>

                    <!-- Item Detail Progres (Warna Kuning & Biru Saja) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <!-- RAB LP-002 (Warna Biru) -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                            <div class="flex justify-between font-bold">
                                <span class="text-slate-600">RAB LP-002 <span class="text-slate-400 font-normal">(5%)</span></span>
                                <span class="text-blue-600">{{ number_format($jobPackage->rab_lp002 ?? 0, 0) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $jobPackage->rab_lp002 ?? 0) }}%"></div>
                            </div>
                        </div>

                        <!-- PBJ LP-002 (Warna Kuning) -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                            <div class="flex justify-between font-bold">
                                <span class="text-slate-600">PBJ LP-002 <span class="text-slate-400 font-normal">(5%)</span></span>
                                <span class="text-amber-500">{{ number_format($jobPackage->pbj_lp002 ?? 0, 0) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-amber-400 h-1.5 rounded-full" style="width: {{ min(100, $jobPackage->pbj_lp002 ?? 0) }}%"></div>
                            </div>
                        </div>

                        <!-- Fisik Pekerjaan (Warna Biru) -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                            <div class="flex justify-between font-bold">
                                <span class="text-slate-600">Fisik Pekerjaan <span class="text-slate-400 font-normal">(85%)</span></span>
                                <span class="text-blue-600">{{ number_format($jobPackage->progress_pekerjaan ?? 0, 0) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $jobPackage->progress_pekerjaan ?? 0) }}%"></div>
                            </div>
                        </div>

                        <!-- ADM Keuangan (Warna Kuning) -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                            <div class="flex justify-between font-bold">
                                <span class="text-slate-600">ADM Keuangan <span class="text-slate-400 font-normal">(5%)</span></span>
                                <span class="text-amber-500">{{ number_format($jobPackage->proses_adm_keuangan ?? 0, 0) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-amber-400 h-1.5 rounded-full" style="width: {{ min(100, $jobPackage->proses_adm_keuangan ?? 0) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Jadwal Pekerjaan -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Jadwal Pekerjaan
                    </h2>

                    <div class="space-y-3 text-xs mt-4">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="text-slate-500 font-bold">Tanggal Mulai Pekerjaan</span>
                            <span class="font-extrabold text-slate-900 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm">
                                {{ $jobPackage->tanggal_mulai_pekerjaan ? \Carbon\Carbon::parse($jobPackage->tanggal_mulai_pekerjaan)->format('d M Y') : '-' }}
                            </span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="text-slate-500 font-bold">Tanggal Selesai Pekerjaan</span>
                            <span class="font-extrabold text-slate-900 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm">
                                {{ $jobPackage->tanggal_selesai_pekerjaan ? \Carbon\Carbon::parse($jobPackage->tanggal_selesai_pekerjaan)->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- BARIS 3: Aktivitas Terkini (KIRI) & Keterangan (KANAN) (Simetris 50:50) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            
            <!-- Kiri: Aktivitas Terkini -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aktivitas Terkini
                    </h2>
                    <div class="text-xs text-slate-700 leading-relaxed bg-blue-50/50 p-4 rounded-xl border border-blue-100 detail-copy mt-3">
                        {{ $jobPackage->latest_activity ?? $jobPackage->aktivitas_terkini ?? 'Belum ada catatan aktivitas terkini.' }}
                    </div>
                </div>
            </div>

            <!-- Kanan: Seksi Keterangan -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Keterangan
                    </h2>
                    <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 detail-copy mt-3">
                        {{ $jobPackage->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                    </div>
                </div>
            </div>

        </div>

        <!-- BARIS 4: Dokumen Pendukung Pekerjaan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Kelengkapan Dokumen
                </h2>
                <span class="text-xs text-slate-500 font-semibold">
                    {{ $docFilled }} dari {{ $docTotal }} Berkas Terlampir
                </span>
            </div>

            <!-- Dokumen Utama (Bagian C) -->
            <div class="space-y-2.5">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Dokumen Utama (Bagian C)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($docFields as $field => $label)
                        @php $hasDoc = !empty($jobPackage->$field); @endphp
                        <div class="p-3.5 {{ $hasDoc ? 'bg-emerald-50/50 border-emerald-100' : 'bg-slate-50 border-slate-100' }} rounded-xl border flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $hasDoc ? 'bg-emerald-500' : 'bg-slate-300' }} shrink-0"></span>
                                <span class="font-bold text-slate-800 truncate">{{ $label }}</span>
                            </div>
                            
                            @if($hasDoc && Route::has('admin.job-packages.download'))
                                <div class="flex items-center gap-2 text-[11px] font-bold shrink-0">
                                    <a href="{{ route('admin.job-packages.download', ['jobPackage' => $jobPackage, 'field' => $field, 'mode' => 'view']) }}" target="_blank" class="text-slate-500 hover:text-blue-900 transition-colors">
                                        Lihat
                                    </a>
                                    <span class="text-slate-300">•</span>
                                    <a href="{{ route('admin.job-packages.download', ['jobPackage' => $jobPackage, 'field' => $field, 'mode' => 'download']) }}" class="text-blue-900 hover:underline">
                                        Unduh
                                    </a>
                                </div>
                            @elseif($hasDoc)
                                <span class="text-[11px] font-bold text-emerald-600 shrink-0">Tersedia</span>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400 italic shrink-0">Belum ada</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Dokumen Tambahan (Bagian D) -->
            <div class="space-y-2.5 pt-3 border-t border-slate-100">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Dokumen Tambahan (Bagian D)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($docPendukung as $field => $label)
                        @php $hasDoc = !empty($jobPackage->$field); @endphp
                        <div class="p-3.5 {{ $hasDoc ? 'bg-emerald-50/50 border-emerald-100' : 'bg-slate-50 border-slate-100' }} rounded-xl border flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $hasDoc ? 'bg-emerald-500' : 'bg-slate-300' }} shrink-0"></span>
                                <span class="font-bold text-slate-800 truncate">{{ $label }}</span>
                            </div>
                            
                            @if($hasDoc && Route::has('admin.job-packages.download'))
                                <div class="flex items-center gap-2 text-[11px] font-bold shrink-0">
                                    <a href="{{ route('admin.job-packages.download', ['jobPackage' => $jobPackage, 'field' => $field, 'mode' => 'view']) }}" target="_blank" class="text-slate-500 hover:text-blue-900 transition-colors">
                                        Lihat
                                    </a>
                                    <span class="text-slate-300">•</span>
                                    <a href="{{ route('admin.job-packages.download', ['jobPackage' => $jobPackage, 'field' => $field, 'mode' => 'download']) }}" class="text-blue-900 hover:underline">
                                        Unduh
                                    </a>
                                </div>
                            @elseif($hasDoc)
                                <span class="text-[11px] font-bold text-emerald-600 shrink-0">Tersedia</span>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400 italic shrink-0">Belum ada</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- BARIS 5: Riwayat Perubahan Aktivitas -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Perubahan
                </h2>
                @if($logsList->isNotEmpty())
                    <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold">
                        {{ count($logsList) }} Aktivitas
                    </span>
                @endif
            </div>

            <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
                @forelse($logsList as $log)
                    @php
                        $action = $log->action ?? 'updated';
                        $actionStyle = match($action) {
                            'created' => 'border-blue-500 text-blue-700',
                            'updated' => 'border-amber-500 text-amber-700',
                            'cancelled' => 'border-rose-500 text-rose-700',
                            'reactivated' => 'border-emerald-500 text-emerald-700',
                            default => 'border-blue-900 text-slate-800',
                        };
                        $causer = $log->user->name ?? $log->causer->name ?? 'Sistem';
                        $timestamp = isset($log->created_at) ? $log->created_at->diffForHumans() : '-';
                        $desc = $log->description ?? $log->keterangan ?? 'Aktivitas diperbarui';
                    @endphp
                    <div class="flex items-start gap-3 text-xs border-l-2 {{ $actionStyle }} pl-4 py-1.5 bg-slate-50/50 rounded-r-xl">
                        <div class="w-full">
                            <span class="font-bold text-slate-900 block leading-snug">{{ $desc }}</span>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">
                                oleh <strong class="text-slate-600">{{ $causer }}</strong> • {{ $timestamp }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-400 font-medium py-4 text-center">
                        Belum ada riwayat aktivitas tercatat.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-admin-layout>