<x-admin-layout title="Detail Job Package">

    <style>
        .job-detail-page { animation: detailPageIn .55s cubic-bezier(.22,1,.36,1) both; }
        .job-detail-page .detail-copy { overflow-wrap:anywhere; white-space:pre-line; line-height:1.65; }
        .job-detail-page .detail-panel { border-color:#e5ebf3; box-shadow:0 7px 24px rgba(15,23,42,.045); }
        .job-detail-page .detail-action { transition:transform .2s ease, box-shadow .2s ease; }
        .job-detail-page .detail-action:hover { transform:translateY(-2px); box-shadow:0 8px 18px rgba(15,23,42,.10); }
        @keyframes detailPageIn { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
        @media (prefers-reduced-motion: reduce) { .job-detail-page { animation:none; } }
    </style>

    @php
    $progress = (float) ($jobPackage->hasil_progres ?? 0);

    $statusBadge = match(true) {
    $progress >= 100 => ['label' => 'Selesai', 'color' => 'emerald'],
    $progress > 0 => ['label' => 'Sedang Berjalan', 'color' => 'amber'],
    default => ['label' => 'Belum Mulai', 'color' => 'slate'],
    };

    $tglSelesai = $jobPackage->tanggal_selesai_pekerjaan
    ? \Carbon\Carbon::parse($jobPackage->tanggal_selesai_pekerjaan)->startOfDay()
    : null;

    $isLate = $tglSelesai && now()->startOfDay()->gt($tglSelesai) && $progress < 100; if ($isLate) {
        $statusBadge=['label'=> 'Terlambat', 'color' => 'rose'];
        }

        $isCancelled = $jobPackage->status === 'batal';
        if ($isCancelled) {
        $statusBadge = ['label' => 'Batal / Belum Ada Tindak Lanjut', 'color' => 'slate'];
        }

        $sisaHari = $tglSelesai
        ? (int) round(now()->startOfDay()->diffInDays($tglSelesai, false))
        : null;

        $oe = (float) ($jobPackage->owner_estimate ?? 0);
        $finalHarga = (float) ($jobPackage->final_harga ?? 0);
        $selisihBudget = $oe - $finalHarga;
        $persenEfisiensi = $oe > 0 ? ($selisihBudget / $oe) * 100 : 0;

        $docFields = ['doc_rab', 'doc_bak', 'doc_surat_permintaan', 'doc_surat_izin_prinsip', 'doc_tor', 'doc_bast'];
        $docFilled = collect($docFields)->filter(fn($f) => !empty($jobPackage->$f))->count();
        $docTotal = count($docFields);
        @endphp

        <div class="job-detail-page">
        <!-- Alert jika Job Package sedang Batal -->
        @if ($isCancelled)
        <div
            class="bg-slate-100 border border-slate-200 text-slate-600 px-4 py-3 rounded-xl text-sm mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">Job Package ini berstatus <strong>Batal</strong>. Data tetap tersimpan lengkap dan
                dapat diaktifkan kembali kapan saja.</span>
        </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('admin.job-packages.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-[#0f2b5c] transition-colors mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <h1
                    class="text-3xl font-black text-slate-900 tracking-tight leading-tight max-w-2xl {{ $isCancelled ? 'text-slate-400' : '' }}">
                    {{ $jobPackage->job_package }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-3">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                    bg-{{ $statusBadge['color'] }}-50 text-{{ $statusBadge['color'] }}-700 border border-{{ $statusBadge['color'] }}-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $statusBadge['color'] }}-500"></span>
                        {{ $statusBadge['label'] }}
                    </span>
                    @foreach($jobPackage->permintaanDaris as $permintaan)
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3c0-1.657-3.134-3-7-3s-7 1.343-7 3v2h14v-2z" />
                        </svg>
                        {{ $permintaan->permintaan_dari }}
                    </span>
                    @endforeach
                    <span class="text-sm text-slate-400">No. PO Utama <span
                            class="font-bold text-slate-600">{{ $jobPackage->no_po ?? '-' }}</span></span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                @if ($isCancelled)
                <form action="{{ route('admin.job-packages.reactivate', $jobPackage) }}" method="POST"
                    onsubmit="return confirm('Aktifkan kembali Job Package ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-2xl font-bold text-sm transition-all shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Aktifkan Kembali
                    </button>
                </form>
                @else
                <form action="{{ route('admin.job-packages.cancel', $jobPackage) }}" method="POST"
                    onsubmit="return confirm('Batalkan Job Package ini? Data tidak akan dihapus dan bisa diaktifkan kembali kapan saja.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 px-5 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Batalkan
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.job-packages.edit', $jobPackage) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-5 py-3 rounded-2xl font-bold text-sm transition-all shadow-md shadow-amber-500/25 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>
            </div>
        </div>

        <!-- Hero: Progress + KPI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
            <!-- Progress Utama -->
            <div
                class="lg:col-span-1 bg-gradient-to-br from-[#0f2b5c] via-[#123268] to-[#16326e] text-white rounded-3xl p-8 shadow-xl shadow-blue-900/10 relative overflow-hidden flex flex-col justify-center {{ $isCancelled ? 'grayscale opacity-75' : '' }}">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 bg-amber-400 rounded-xl shadow-lg shadow-amber-500/30">
                            <svg class="w-4 h-4 text-[#0f2b5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-amber-300 uppercase tracking-widest">Progres Pekerjaan</p>
                    </div>
                    <div class="flex items-start gap-1 mb-5">
                        <span
                            class="text-8xl font-black leading-[0.85] tracking-tighter">{{ number_format($progress, 0, ',', '.') }}</span>
                        <span class="text-3xl font-bold text-amber-400 mt-2">%</span>
                    </div>
                    <div class="w-full bg-white/10 h-2.5 rounded-full overflow-hidden mb-4">
                        <div class="bg-amber-400 h-full rounded-full transition-all duration-700 shadow-[0_0_8px_rgba(251,191,36,0.6)]"
                            style="width: {{ min(100, max(0, $progress)) }}%;"></div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Diinput oleh {{ $jobPackage->creator->name ?? 'Admin JPP' }}
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full"></div>
                <div class="absolute -right-4 -top-10 w-24 h-24 bg-amber-400/10 rounded-full"></div>
            </div>

            <!-- 3 KPI dengan ikon -->
            <div
                class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div
                            class="p-2 {{ $selisihBudget >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Anggaran</p>
                    </div>
                    <p
                        class="text-3xl font-black {{ $selisihBudget >= 0 ? 'text-emerald-600' : 'text-rose-600' }} leading-none">
                        {{ number_format(abs($persenEfisiensi), 0, ',', '.') }}<span class="text-lg">%</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-1.5">
                        {{ $selisihBudget >= 0 ? 'lebih hemat dari OE' : 'melebihi Owner Estimate' }}</p>
                </div>

                <div class="sm:border-l sm:border-slate-100 sm:pl-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div
                            class="p-2 {{ $isLate ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }} rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Jadwal</p>
                    </div>
                    @if($sisaHari === null)
                    <p class="text-3xl font-black text-slate-300 leading-none">-</p>
                    <p class="text-xs text-slate-400 mt-1.5">tanggal selesai belum diisi</p>
                    @elseif($isLate)
                    <p class="text-3xl font-black text-rose-600 leading-none">{{ abs($sisaHari) }}<span class="text-lg">
                            hr</span></p>
                    <p class="text-xs text-rose-500 mt-1.5">telah melewati tenggat</p>
                    @elseif($progress >= 100)
                    <p class="text-2xl font-black text-emerald-600 leading-none">Tuntas</p>
                    <p class="text-xs text-slate-400 mt-1.5">pekerjaan selesai</p>
                    @else
                    <p class="text-3xl font-black text-slate-800 leading-none">{{ abs($sisaHari) }}<span
                            class="text-lg"> hr</span></p>
                    <p class="text-xs text-slate-400 mt-1.5">tersisa hingga tenggat</p>
                    @endif
                </div>

                <div class="sm:border-l sm:border-slate-100 sm:pl-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Dokumen</p>
                    </div>
                    <p class="text-3xl font-black text-slate-800 leading-none">{{ $docFilled }}<span
                            class="text-slate-300">/{{ $docTotal }}</span></p>
                    <p class="text-xs text-slate-400 mt-1.5">berkas terlampir</p>
                </div>
            </div>
        </div>

        <!-- Body: 2 kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Kolom Kiri -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <h4 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-[#0f2b5c] rounded-full"></span>
                        Identitas Pekerjaan
                    </h4>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-400 mb-1.5">No. Surat / BAK / Doc</dt>
                            @if($jobPackage->suratBakDocs->isNotEmpty())
                            <dd class="space-y-1.5">
                                @foreach($jobPackage->suratBakDocs as $index => $surat)
                                <div class="flex gap-2 items-start">
                                    <span
                                        class="shrink-0 text-[10px] font-bold text-slate-400 mt-0.5">{{ $index + 1 }}.</span>
                                    <span
                                        class="font-semibold text-slate-700 text-xs leading-relaxed">{{ $surat->no_surat_bak_doc }}</span>
                                </div>
                                @endforeach
                            </dd>
                            @else
                            <dd class="font-semibold text-slate-700">-</dd>
                            @endif
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 mb-1.5">Permintaan Dari</dt>
                            @if($jobPackage->permintaanDaris->isNotEmpty())
                            <dd class="space-y-1">
                                @foreach($jobPackage->permintaanDaris as $permintaan)
                                <div class="flex gap-1.5 items-start">
                                    <span class="text-slate-400 text-xs mt-0.5">-</span>
                                    <span
                                        class="font-semibold text-slate-700 text-xs leading-relaxed">{{ $permintaan->permintaan_dari }}</span>
                                </div>
                                @endforeach
                            </dd>
                            @else
                            <dd class="font-semibold text-slate-700">-</dd>
                            @endif
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 mb-0.5">Tanggal Surat Masuk</dt>
                            <dd class="font-semibold text-slate-700">
                                {{ optional($jobPackage->tanggal_surat_masuk)->format('d M Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 mb-0.5">No. Service Notifikasi</dt>
                            <dd class="font-semibold text-slate-700 font-mono">
                                {{ $jobPackage->no_service_notifikasi ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 mb-0.5">No. Service Order (SO)</dt>
                            <dd class="font-semibold text-slate-700 font-mono">
                                {{ $jobPackage->no_service_order ?? '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-5 border-t border-slate-100 space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs text-slate-400 shrink-0">Owner Estimate</p>
                            <p class="text-base font-black text-[#0f2b5c] text-right tabular-nums whitespace-nowrap">
                                Rp {{ number_format($oe, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs text-slate-400 shrink-0">Final Harga</p>
                            <p class="text-base font-black text-emerald-600 text-right tabular-nums whitespace-nowrap">
                                {{ $finalHarga > 0 ? 'Rp ' . number_format($finalHarga, 0, ',', '.') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <h4 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                        Jadwal & Bobot Pekerjaan
                    </h4>
                    <dl class="space-y-3 text-sm mb-6">
                        <div class="flex justify-between items-center">
                            <dt class="text-slate-500">Tanggal Mulai</dt>
                            <dd class="font-semibold text-slate-800">
                                {{ optional($jobPackage->tanggal_mulai_pekerjaan)->format('d M Y') ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-slate-500">Tanggal Selesai</dt>
                            <dd class="font-semibold text-slate-800">
                                {{ optional($jobPackage->tanggal_selesai_pekerjaan)->format('d M Y') ?? '-' }}</dd>
                        </div>
                    </dl>

                    <div class="space-y-4">
                        @php
                        $bobot = [
                        ['label' => 'RAB LP-002', 'sub' => 'Bobot 5%', 'value' => $jobPackage->rab_lp002 ?? 0, 'color'
                        => 'indigo'],
                        ['label' => 'PB/J LP-002', 'sub' => 'Bobot 5%', 'value' => $jobPackage->pbj_lp002 ?? 0, 'color'
                        => 'purple'],
                        ['label' => 'Fisik Pekerjaan', 'sub' => 'Bobot 85%', 'value' => $jobPackage->progress_pekerjaan
                        ?? 0, 'color' => 'amber'],
                        ['label' => 'ADM Keuangan', 'sub' => 'Bobot 5%', 'value' => $jobPackage->proses_adm_keuangan ??
                        0, 'color' => 'sky'],
                        ];
                        @endphp
                        @foreach ($bobot as $b)
                        @php
                        $barColorClass = match($b['color']) {
                        'indigo' => 'bg-indigo-500',
                        'purple' => 'bg-purple-500',
                        'amber' => 'bg-amber-500',
                        'sky' => 'bg-sky-500',
                        default => 'bg-slate-500',
                        };
                        @endphp
                        <div>
                            <div class="flex justify-between items-baseline mb-1.5">
                                <span class="text-xs font-semibold text-slate-600">{{ $b['label'] }} <span
                                        class="text-slate-300 font-normal">· {{ $b['sub'] }}</span></span>
                                <span
                                    class="text-xs font-bold text-slate-700">{{ number_format($b['value'], 0, ',', '.') }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColorClass }} rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $b['value']) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Detail PO -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100">
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-[#0f2b5c] rounded-full"></span>
                            Rincian Purchase Order
                        </h4>
                        <span
                            class="text-xs font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-full">{{ $jobPackage->pos->count() }}
                            item</span>
                    </div>

                    @if ($jobPackage->pos->isNotEmpty())
                    <div class="p-4 space-y-2">
                        @foreach ($jobPackage->pos as $index => $po)
                        <div x-data="{ open: false }" class="rounded-2xl hover:bg-slate-50 transition-colors">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between gap-4 px-3 py-3.5 text-left">
                                <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                    <span
                                        class="shrink-0 w-9 h-9 rounded-full bg-[#0f2b5c] text-white text-sm font-black flex items-center justify-center shadow-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-800 text-sm truncate">
                                            {{ $po->description ?? 'Tanpa deskripsi' }}</p>
                                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $po->no_po ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-bold text-emerald-600 text-sm tabular-nums whitespace-nowrap">Rp
                                        {{ number_format($po->price ?? 0, 0, ',', '.') }}</span>
                                    <svg class="w-4 h-4 text-slate-300 transition-transform shrink-0"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>
                            <div x-show="open" x-collapse x-cloak class="px-3 pb-3.5">
                                @php
                                $totalPo = $jobPackage->pos->sum('price');
                                $percentage = $totalPo > 0 ? (($po->price ?? 0) / $totalPo) * 100 : 0;
                                @endphp
                                <div class="bg-slate-50 rounded-xl p-3.5 flex items-center gap-3">
                                    <span class="text-xs text-slate-500 shrink-0">Porsi dari total PO</span>
                                    <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-slate-600 shrink-0">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div
                        class="px-6 py-5 bg-gradient-to-r from-[#0f2b5c]/[0.03] to-amber-500/[0.05] border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">Total keseluruhan PO</span>
                        <span class="text-2xl font-black text-[#0f2b5c] tabular-nums">Rp
                            {{ number_format($jobPackage->pos->sum('price'), 0, ',', '.') }}</span>
                    </div>
                    @else
                    <p class="px-6 py-10 text-center text-sm text-slate-400 italic">Belum ada data Purchase Order.</p>
                    @endif
                </div>

                <!-- Dokumen -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                            Dokumen Pendukung
                        </h4>
                        <span class="text-xs font-bold text-slate-400">{{ $docFilled }}/{{ $docTotal }} lengkap</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @php
                        $docs = [
                        'doc_rab' => 'RAB',
                        'doc_bak' => 'BAK / Negosiasi',
                        'doc_surat_permintaan' => 'Surat Permintaan',
                        'doc_surat_izin_prinsip' => 'Surat Izin Prinsip',
                        'doc_tor' => 'TOR',
                        'doc_bast' => 'BAST',
                        ];
                        @endphp
                        @foreach ($docs as $field => $label)
                        <div
                            class="flex items-center justify-between px-4 py-3.5 rounded-2xl {{ $jobPackage->$field ? 'bg-emerald-50/60' : 'bg-slate-50' }}">
                            <span class="text-sm font-semibold text-slate-700 flex items-center gap-2.5">
                                <span
                                    class="w-2 h-2 rounded-full {{ $jobPackage->$field ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                {{ $label }}
                            </span>
                            @if ($jobPackage->$field)
                            <div class="flex items-center gap-1.5 text-xs font-bold">
                                <a href="{{ route('admin.job-packages.download', ['jobPackage' => $jobPackage, 'field' => $field, 'mode' => 'view']) }}"
                                    target="_blank" class="text-slate-500 hover:text-[#0f2b5c]">Lihat</a>
                                <span class="text-slate-300">·</span>
                                <a href="{{ route('admin.job-packages.download', ['jobPackage' => $jobPackage, 'field' => $field, 'mode' => 'download']) }}"
                                    class="text-[#0f2b5c] hover:text-blue-900">Unduh</a>
                            </div>
                            @else
                            <span class="text-xs text-slate-400 italic">Belum ada</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Aktivitas & Keterangan -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Aktivitas Terkini
                    </h4>
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line mb-6">
                        {{ $jobPackage->latest_activity ?: 'Belum ada catatan aktivitas terkini.' }}
                    </p>

                    <h4 class="text-sm font-bold text-slate-800 mb-3 pt-5 border-t border-slate-100">Keterangan</h4>
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                        {{ $jobPackage->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>

                <!-- Riwayat Perubahan -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-slate-400 rounded-full"></span>
                            Riwayat Perubahan
                        </h4>
                        @if ($jobPackage->activityLogs->count() > 0)
                        <span class="text-[11px] font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-full">
                            {{ $jobPackage->activityLogs->count() }} aktivitas
                        </span>
                        @endif
                    </div>

                    @if ($jobPackage->activityLogs->isNotEmpty())
                    <div class="space-y-4 max-h-[420px] overflow-y-auto pr-2 -mr-2"
                        style="scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">
                        @foreach ($jobPackage->activityLogs->take(30) as $log)
                        @php
                        $actionStyle = match($log->action) {
                        'created' => ['bg-blue-500', 'Dibuat'],
                        'updated' => ['bg-amber-500', 'Diperbarui'],
                        'cancelled' => ['bg-rose-500', 'Dibatalkan'],
                        'reactivated' => ['bg-emerald-500', 'Diaktifkan'],
                        'document_uploaded' => ['bg-purple-500', 'Dokumen'],
                        'document_deleted' => ['bg-slate-400', 'Dokumen'],
                        default => ['bg-slate-400', 'Aktivitas'],
                        };
                        @endphp
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center shrink-0">
                                <span class="w-2.5 h-2.5 rounded-full {{ $actionStyle[0] }} mt-1.5"></span>
                                @if (!$loop->last)
                                <span class="w-px flex-1 bg-slate-100 mt-1"></span>
                                @endif
                            </div>
                            <div class="pb-4 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold text-slate-700">{{ $actionStyle[1] }}</span>
                                    <span class="text-[11px] text-slate-400">oleh
                                        {{ $log->user->name ?? 'Sistem' }}</span>
                                    <span class="text-[11px] text-slate-300">·</span>
                                    <span
                                        class="text-[11px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $log->description }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if ($jobPackage->activityLogs->count() > 30)
                    <p class="text-[11px] text-slate-400 text-center mt-4 pt-3 border-t border-slate-100">
                        Menampilkan 30 aktivitas terbaru dari {{ $jobPackage->activityLogs->count() }} total riwayat
                    </p>
                    @endif
                    @else
                    <p class="text-xs text-slate-400 italic">Belum ada riwayat perubahan.</p>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </x-admin-layout>