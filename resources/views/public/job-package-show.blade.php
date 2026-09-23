<x-guest-layout title="Detail Job Package">
    @php
        $progress = max(0, min(100, (float) $jobPackage->hasil_progres));
        $status = $progress >= 100 ? 'Selesai' : ($progress > 0 ? 'Sedang Berjalan' : 'Belum Mulai');
        $statusClass = $progress >= 100 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : ($progress > 0 ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-slate-600 bg-slate-100 border-slate-200');
    @endphp

    <style>
        .public-detail { animation: detailIn .55s cubic-bezier(.22,1,.36,1) both; }
        .public-detail .detail-panel { border:1px solid #e5ebf3; box-shadow:0 7px 24px rgba(15,23,42,.045); }
        .public-detail .detail-value { overflow-wrap:anywhere; white-space:pre-line; line-height:1.6; }
        @keyframes detailIn { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    </style>

    <div class="public-detail mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('home') }}#table-section" class="text-sm font-bold text-slate-500 hover:text-blue-700">← Kembali ke monitoring</a>
                <p class="mt-5 text-xs font-bold uppercase tracking-[.18em] text-blue-600">Detail Job Package</p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ $jobPackage->job_package }}</h1>
            </div>
            <span class="inline-flex w-fit rounded-full border px-4 py-2 text-xs font-black {{ $statusClass }}">{{ $status }} · {{ number_format($progress, 0) }}%</span>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
            <div class="detail-panel rounded-2xl bg-white p-6 sm:p-8">
                <h2 class="mb-6 flex items-center gap-2 text-base font-black text-slate-900"><span class="h-5 w-1.5 rounded-full bg-[#0f2b5c]"></span> Informasi Pekerjaan</h2>
                <dl class="grid gap-5 sm:grid-cols-2">
                    <div><dt class="text-xs font-bold uppercase text-slate-400">No. Surat / BAK / Doc</dt><dd class="detail-value mt-1 text-sm font-semibold text-slate-700">{{ $jobPackage->suratBakDocs->pluck('no_surat_bak_doc')->join("\n") ?: ($jobPackage->no_surat_bak_doc ?: '-') }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-slate-400">Permintaan Dari</dt><dd class="detail-value mt-1 text-sm font-semibold text-slate-700">{{ $jobPackage->permintaanDaris->pluck('permintaan_dari')->join(', ') ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-slate-400">No. Service Order</dt><dd class="mt-1 font-mono text-sm font-bold text-slate-800">{{ $jobPackage->no_service_order ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-slate-400">No. Service Notifikasi</dt><dd class="mt-1 font-mono text-sm font-bold text-slate-800">{{ $jobPackage->no_service_notifikasi ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-slate-400">Owner Estimate</dt><dd class="mt-1 text-base font-black text-[#0f2b5c]">Rp {{ number_format((float) $jobPackage->owner_estimate, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-slate-400">Final Harga</dt><dd class="mt-1 text-base font-black text-emerald-600">{{ $jobPackage->final_harga ? 'Rp ' . number_format((float) $jobPackage->final_harga, 0, ',', '.') : '-' }}</dd></div>
                </dl>
            </div>

            <div class="detail-panel rounded-2xl bg-white p-6 sm:p-8">
                <h2 class="mb-6 flex items-center gap-2 text-base font-black text-slate-900"><span class="h-5 w-1.5 rounded-full bg-amber-500"></span> Jadwal & Progress</h2>
                <div class="space-y-4 text-sm text-slate-600">
                    <p><b class="text-slate-800">Mulai:</b> {{ optional($jobPackage->tanggal_mulai_pekerjaan)->format('d M Y') ?: '-' }}</p>
                    <p><b class="text-slate-800">Target:</b> {{ optional($jobPackage->tanggal_selesai_pekerjaan)->format('d M Y') ?: '-' }}</p>
                    <div class="pt-2"><div class="mb-2 flex justify-between text-xs font-black uppercase text-slate-500"><span>Hasil Progres</span><span class="text-blue-700">{{ number_format($progress, 0) }}%</span></div><div class="h-3 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-blue-600" style="width:{{ $progress }}%"></div></div></div>
                </div>
            </div>
        </div>

        <div class="detail-panel rounded-2xl bg-white p-6 sm:p-8">
            <h2 class="mb-5 flex items-center gap-2 text-base font-black text-slate-900"><span class="h-5 w-1.5 rounded-full bg-blue-500"></span> Aktivitas Terkini</h2>
            <p class="detail-value text-sm text-slate-600">{{ $jobPackage->latest_activity ?: 'Belum ada catatan aktivitas terkini.' }}</p>
        </div>
    </div>
</x-guest-layout>
