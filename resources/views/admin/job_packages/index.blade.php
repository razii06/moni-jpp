<x-admin-layout title="Kelola Job Package">
    <div class="space-y-6 w-full">
        <!-- Header Page -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Job Package</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar seluruh pekerjaan Jasa Pelayanan Pabrik berdasarkan status.</p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.job-packages.export') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>

                <!-- Tombol Tambah Job Package dengan Gate 'create-data' -->
                @can('create-data')
                    <a href="{{ route('admin.job-packages.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-900/20 transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Job Package
                    </a>
                @endcan
            </div>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-bold text-emerald-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Filter & Search Panel -->
        @php $currentStatusFilter = request('status_filter', 'semua'); @endphp
        <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 flex-wrap">
                @php
                    $filters = [
                        'semua' => 'Semua',
                        'belum_mulai' => 'Belum Mulai',
                        'sedang_berjalan' => 'Sedang Berjalan',
                        'selesai' => 'Selesai',
                        'batal' => 'Dibatalkan'
                    ];
                @endphp
                @foreach($filters as $value => $label)
                    <a href="{{ request()->fullUrlWithQuery(['status_filter' => $value]) }}#table-section" 
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ $currentStatusFilter === $value ? 'bg-blue-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Search & Sort Form -->
            <form method="GET" action="{{ route('admin.job-packages.index') }}#table-section" class="flex items-center gap-2 flex-wrap md:flex-nowrap">
                <input type="hidden" name="status_filter" value="{{ $currentStatusFilter }}">
                
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Job Package, SO, PO..." class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <select name="sort" onchange="this.form.submit()" class="py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama Ditambahkan</option>
                    <option value="progress_desc" {{ request('sort') === 'progress_desc' ? 'selected' : '' }}>Progres Tertinggi</option>
                    <option value="progress_asc" {{ request('sort') === 'progress_asc' ? 'selected' : '' }}>Progres Terendah</option>
                    <option value="oe_desc" {{ request('sort') === 'oe_desc' ? 'selected' : '' }}>OE Tertinggi</option>
                    <option value="oe_asc" {{ request('sort') === 'oe_asc' ? 'selected' : '' }}>OE Terendah</option>
                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Nama A-Z</option>
                </select>
            </form>
        </div>

        <div id="table-section"></div>

        @php
            // Definisi 4 Tabel Utama
            $boards = [
                [
                    'id' => 'table-not-started',
                    'key' => 'not_started',
                    'header_bg' => 'bg-slate-50 border-slate-200 text-slate-800',
                    'title' => 'Pekerjaan Belum Mulai (0%)',
                    'items' => $notStartedJobs ?? collect(),
                    'empty' => 'Tidak ada pekerjaan yang belum mulai.',
                    'badge_bg' => 'bg-slate-100 text-slate-700 border border-slate-200',
                    'bar_bg' => 'bg-slate-400',
                    'icon_path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    'status_label' => 'Belum Mulai'
                ],
                [
                    'id' => 'table-running',
                    'key' => 'running',
                    'header_bg' => 'bg-blue-50 border-blue-100 text-blue-800',
                    'title' => 'Pekerjaan Sedang Berjalan (1-99%)',
                    'items' => $runningJobs ?? collect(),
                    'empty' => 'Tidak ada pekerjaan yang sedang berjalan.',
                    'badge_bg' => 'bg-blue-50 text-blue-700 border border-blue-200',
                    'bar_bg' => 'bg-blue-600',
                    'icon_path' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    'status_label' => 'Sedang Berjalan'
                ],
                [
                    'id' => 'table-done',
                    'key' => 'done',
                    'header_bg' => 'bg-emerald-50 border-emerald-100 text-emerald-800',
                    'title' => 'Pekerjaan Selesai (100%)',
                    'items' => $doneJobs ?? collect(),
                    'empty' => 'Belum ada pekerjaan yang selesai.',
                    'badge_bg' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                    'bar_bg' => 'bg-emerald-500',
                    'icon_path' => 'M5 13l4 4L19 7',
                    'status_label' => 'Selesai'
                ],
                [
                    'id' => 'table-cancelled',
                    'key' => 'cancelled',
                    'header_bg' => 'bg-rose-50 border-rose-100 text-rose-800',
                    'title' => 'Pekerjaan Tidak Dilanjutkan (Dibatalkan)',
                    'items' => $cancelledJobs ?? collect(),
                    'empty' => 'Tidak ada pekerjaan yang dibatalkan.',
                    'badge_bg' => 'bg-rose-50 text-rose-700 border border-rose-200',
                    'bar_bg' => 'bg-rose-500',
                    'icon_path' => 'M6 18L18 6M6 6l12 12',
                    'status_label' => 'Dibatalkan'
                ],
            ];

            // Logika Penyaringan Board Berdasarkan Tab Filter Aktif
            if ($currentStatusFilter === 'belum_mulai') {
                $boards = array_filter($boards, fn($b) => $b['key'] === 'not_started');
            } elseif ($currentStatusFilter === 'sedang_berjalan') {
                $boards = array_filter($boards, fn($b) => $b['key'] === 'running');
            } elseif ($currentStatusFilter === 'selesai') {
                $boards = array_filter($boards, fn($b) => $b['key'] === 'done');
            } elseif ($currentStatusFilter === 'batal') {
                $boards = array_filter($boards, fn($b) => $b['key'] === 'cancelled');
            }

            // Fungsi Ekstraksi Nomor PO
            $getPoNumber = function($poItem) {
                if (is_object($poItem)) return $poItem->no_po ?? $poItem->po_number ?? $poItem->nomor_po ?? $poItem->no_po_doc ?? '-';
                if (is_array($poItem)) return $poItem['no_po'] ?? $poItem['po_number'] ?? $poItem['nomor_po'] ?? $poItem['no_po_doc'] ?? '-';
                return is_scalar($poItem) ? (string)$poItem : '-';
            };

            // Fungsi Ekstraksi Nilai PO
            $getPoPrice = function($poItem) {
                $rawVal = is_object($poItem) 
                    ? ($poItem->price ?? $poItem->nilai_kontrak ?? $poItem->total_harga_po ?? $poItem->hps ?? $poItem->nilai_pekerjaan ?? $poItem->nilai_po_netto ?? $poItem->nilai_po_rp ?? $poItem->harga_total ?? $poItem->amount ?? $poItem->nilai_spk ?? $poItem->nilai_po ?? $poItem->harga ?? $poItem->nominal ?? $poItem->nilai ?? $poItem->total ?? $poItem->harga_po ?? $poItem->total_harga ?? null)
                    : ($poItem['price'] ?? $poItem['nilai_kontrak'] ?? $poItem['total_harga_po'] ?? $poItem['hps'] ?? $poItem['nilai_pekerjaan'] ?? $poItem['nilai_po_netto'] ?? $poItem['nilai_po_rp'] ?? $poItem['harga_total'] ?? $poItem['amount'] ?? $poItem['nilai_spk'] ?? $poItem['nilai_po'] ?? $poItem['harga'] ?? $poItem['nominal'] ?? $poItem['nilai'] ?? $poItem['total'] ?? $poItem['harga_po'] ?? $poItem['total_harga'] ?? null);

                if ($rawVal !== null && $rawVal !== '') {
                    $cleaned = preg_replace('/[^0-9]/', '', (string)$rawVal);
                    if ($cleaned !== '') return 'Rp ' . number_format((float)$cleaned, 0, ',', '.');
                }
                return null;
            };
        @endphp

        <!-- Looping Tabel berdasarkan Status Card -->
        @foreach($boards as $board)
            <div id="{{ $board['id'] }}" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden scroll-mt-20">
                <!-- Status Card Header -->
                <div class="p-4 border-b font-bold flex items-center gap-2 {{ $board['header_bg'] }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $board['icon_path'] }}"/>
                    </svg>
                    <span>{{ $board['title'] }}</span>
                    @if(method_exists($board['items'], 'total'))
                        <span class="ml-2 px-2 py-0.5 rounded-full bg-white/50 text-sm border border-current opacity-80">{{ $board['items']->total() }}</span>
                    @endif
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap min-w-[1100px]">
                        <thead class="bg-slate-50 text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 w-10 text-center">NO.</th>
                                <th class="px-4 py-3 min-w-[280px]">JOB PACKAGE (SM01)</th>
                                <th class="px-4 py-3 min-w-[200px]">NO. SERVICE ORDER & NOTIFIKASI</th>
                                <th class="px-4 py-3 min-w-[140px]">OWNER ESTIMATE</th>
                                <th class="px-4 py-3 min-w-[140px]">NO. PO</th>
                                <th class="px-4 py-3 min-w-[180px]">STATUS & PROGRES</th>
                                <th class="px-4 py-3 text-center min-w-[180px]">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($board['items'] as $jp)
                                @php
                                    // Hitung Progres
                                    $progress = floatval(str_replace(',', '.', $jp->hasil_progres ?? 0));
                                    $progress = max(0, min(100, $progress));
                                    
                                    $isCancelled = ($jp->status === 'batal');

                                    // Ekstraksi Item Job Package (SM01)
                                    $jpList = [];
                                    $rawJp = $jp->job_package ?? null;
                                    if (!empty($rawJp)) {
                                        if (is_array($rawJp)) {
                                            $jpList = $rawJp;
                                        } else {
                                            $decoded = json_decode($rawJp, true);
                                            $jpList = is_array($decoded) ? $decoded : preg_split('/[\n\r,]+/', $rawJp);
                                        }
                                    }
                                    $jpList = array_values(array_filter(array_map('trim', (array)$jpList)));
                                    $firstJp = $jpList[0] ?? 'Tanpa Judul Pekerjaan';
                                    $extraJpCount = max(0, count($jpList) - 1);

                                    // Ekstraksi PO & Harga
                                    $pos = $jp->pos;
                                    $firstPo = $pos ? $pos->first() : null;
                                    $extraPoCount = $pos ? ($pos->count() - 1) : 0;
                                @endphp
                                <tr x-data="{ openDetailModal: false }" class="hover:bg-slate-50/80 transition-colors">
                                    <!-- NO -->
                                    <td class="px-4 py-4 font-bold text-slate-400 text-center align-top text-xs">
                                        {{ method_exists($board['items'], 'firstItem') ? ($board['items']->firstItem() + $loop->index) : ($loop->index + 1) }}
                                    </td>

                                    <!-- KOLOM 1: JOB Package (SM01) -->
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex items-start gap-1.5 flex-wrap max-w-sm">
                                            <a href="{{ route('admin.job-packages.show', $jp) }}" class="font-bold text-slate-900 hover:text-blue-700 leading-snug whitespace-normal block transition-colors text-sm">
                                                {{ $firstJp }}
                                            </a>

                                            @if($extraJpCount > 0)
                                                <button @click="openDetailModal = true" type="button" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors shrink-0">
                                                    +{{ $extraJpCount }} lainnya
                                                </button>
                                            @endif

                                            @if(($jp->visibility ?? 'public') === 'internal')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200 shrink-0">
                                                    🔒 Privat
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Modal Rincian Job Package -->
                                        @if($extraJpCount > 0)
                                            <template x-teleport="body">
                                                <div x-show="openDetailModal" 
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" 
                                                    style="display: none;">
                                                    <div @click.outside="openDetailModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 flex flex-col max-h-[85vh]">
                                                        <div class="bg-blue-900 text-white p-4 flex items-center justify-between shrink-0">
                                                            <div>
                                                                <h3 class="font-bold text-base tracking-tight">Rincian Job Package</h3>
                                                                <p class="text-xs text-blue-200 mt-0.5">Berisi {{ count($jpList) }} Paket Pekerjaan</p>
                                                            </div>
                                                            <button @click="openDetailModal = false" type="button" class="text-blue-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-4 overflow-y-auto flex-1">
                                                            <table class="w-full text-left text-xs border-collapse">
                                                                <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 sticky top-0">
                                                                    <tr>
                                                                        <th class="py-2.5 px-3 w-12 text-center font-bold"></th>
                                                                        <th class="py-2.5 px-3 font-bold">NAMA JOB PACKAGE</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-slate-100">
                                                                    @foreach($jpList as $idx => $item)
                                                                        <tr>
                                                                            <td class="py-3 px-3 text-center text-slate-400 font-bold align-top">-</td>
                                                                            <td class="py-3 px-3 font-semibold text-slate-800 leading-relaxed whitespace-normal align-top">{{ $item }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
                                                            <button @click="openDetailModal = false" type="button" class="px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition-colors shadow-sm">
                                                                Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        @endif
                                    </td>

                                    <!-- KOLOM 2: NO. SERVICE ORDER & NOTIFIKASI -->
                                    <td class="px-4 py-4 align-top text-xs space-y-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-slate-400 text-[11px]">Service Order</span>
                                            <span class="font-bold text-slate-800 select-all">{{ $jp->no_service_order ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-slate-400 text-[11px]">Notifikasi</span>
                                            <span class="font-semibold text-slate-800 select-all">{{ $jp->no_service_notifikasi ?? '-' }}</span>
                                        </div>
                                    </td>

                                    <!-- KOLOM 3: OWNER ESTIMATE -->
                                    <td class="px-4 py-4 align-top text-xs">
                                        <span class="font-extrabold text-slate-900">
                                            Rp {{ number_format($jp->owner_estimate ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <!-- KOLOM 4: NO. PO -->
                                    <td class="px-4 py-4 align-top text-xs">
                                        <div class="flex items-center gap-1.5 flex-wrap" x-data="{ openPO: false }">
                                            @if($pos && $pos->count() > 0)
                                                <span class="font-bold text-blue-900 select-all">{{ $getPoNumber($firstPo) }}</span>
                                                @if($extraPoCount > 0)
                                                    <div class="relative inline-block">
                                                        <button @click="openPO = !openPO" @click.outside="openPO = false" type="button" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 hover:bg-amber-200 border border-amber-300 transition-colors">
                                                            +{{ $extraPoCount }} PO
                                                        </button>
                                                        <div x-show="openPO" x-transition class="absolute left-0 mt-1 w-60 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5 z-30" style="display: none;">
                                                            <div class="flex items-center justify-between mb-2 pb-1 border-b border-slate-100">
                                                                <span class="text-[10px] font-extrabold uppercase text-slate-400">Daftar PO & Harga</span>
                                                                <span class="text-[10px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded-full font-bold border border-amber-200">{{ $pos->count() }} Total</span>
                                                            </div>
                                                            <ul class="space-y-1 max-h-44 overflow-y-auto pr-1">
                                                                @foreach($pos as $poItem)
                                                                    @php
                                                                        $poNum = $getPoNumber($poItem);
                                                                        $poPrice = $getPoPrice($poItem);
                                                                    @endphp
                                                                    <li class="flex items-center justify-between gap-2 text-[11px] font-semibold text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                                                                        <span class="font-bold text-slate-800 select-all">{{ $poNum }}</span>
                                                                        @if($poPrice)
                                                                            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 shrink-0">{{ $poPrice }}</span>
                                                                        @else
                                                                            <span class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded shrink-0">Tanpa Harga</span>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif(!empty($jp->no_po))
                                                <span class="font-bold text-blue-900 select-all">{{ $jp->no_po }}</span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- KOLOM 5: STATUS & PROGRES -->
                                    <td class="px-4 py-4 align-top space-y-2">
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $board['badge_bg'] }}">
                                                {{ $board['status_label'] }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden flex-1">
                                                <div class="h-2 rounded-full {{ $board['bar_bg'] }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs font-extrabold text-slate-700 min-w-[36px] text-right">
                                                {{ number_format($progress, 1) }}%
                                            </span>
                                        </div>

                                        <div class="text-[10px] text-slate-400">
                                            PIC: <span class="font-semibold text-slate-600">{{ $jp->creator->name ?? 'Administrator' }}</span>
                                        </div>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="px-4 py-4 align-top text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <a href="{{ route('admin.job-packages.show', $jp) }}" title="Detail Job Package" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Detail</span>
                                            </a>

                                            <!-- Tombol Edit dengan Gate 'edit-data' -->
                                            @can('edit-data')
                                                <a href="{{ route('admin.job-packages.edit', $jp) }}" title="Edit Job Package" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>
                                            @endcan
                                            
                                            @if($isCancelled)
                                                <form method="POST" action="{{ route('admin.job-packages.reactivate', $jp) }}" class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" title="Aktifkan Kembali Job Package" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        <span>Restore</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.job-packages.cancel', $jp) }}" class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" title="Batalkan Job Package" class="px-2.5 py-1.5 bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-700 border border-slate-200 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        <span>Batal</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Tombol Hapus dengan Gate 'delete-data' -->
                                            @can('delete-data')
                                                <form method="POST" action="{{ route('admin.job-packages.destroy', $jp) }}" onsubmit="return confirm('Hapus Job Package ini secara permanen?')" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" title="Hapus Permanen" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                                        {{ $board['empty'] }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Per Tabel -->
                @if(method_exists($board['items'], 'hasPages') && $board['items']->hasPages())
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 font-medium">
                            Menampilkan <span class="font-bold text-slate-700">{{ $board['items']->firstItem() }}</span> - <span class="font-bold text-slate-700">{{ $board['items']->lastItem() }}</span> dari <span class="font-bold text-slate-700">{{ $board['items']->total() }}</span> data
                        </div>
                        
                        <nav role="navigation" aria-label="Pagination Navigation" class="inline-flex items-center rounded-lg border border-slate-200 bg-white overflow-hidden shadow-sm">
                            @if ($board['items']->onFirstPage())
                                <span class="px-3 py-1.5 text-xs font-bold text-slate-300 cursor-not-allowed bg-slate-50">«</span>
                            @else
                                <a href="{{ $board['items']->previousPageUrl() }}#{{ $board['id'] }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">«</a>
                            @endif

                            @foreach ($board['items']->getUrlRange(1, $board['items']->lastPage()) as $page => $url)
                                @if ($page == $board['items']->currentPage())
                                    <span class="px-3 py-1.5 text-xs font-extrabold bg-blue-900 text-white border-l border-slate-200">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}#{{ $board['id'] }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-100 border-l border-slate-200 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($board['items']->hasMorePages())
                                <a href="{{ $board['items']->nextPageUrl() }}#{{ $board['id'] }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-100 border-l border-slate-200 transition-colors">»</a>
                            @else
                                <span class="px-3 py-1.5 text-xs font-bold text-slate-300 cursor-not-allowed bg-slate-50 border-l border-slate-200">»</span>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-admin-layout>