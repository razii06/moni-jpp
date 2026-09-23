<x-admin-layout title="Kelola Job Package">
    <style>
        .jobs-reference { width: 100%; }
        .jobs-reference .jobs-head { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:24px; }
        .jobs-reference .jobs-head h1 { margin:0; color:#0b1120; font-size:1.85rem; font-weight:900; }
        .jobs-reference .jobs-head p { margin:5px 0 0; color:#64748b; font-size:.82rem; }
        .jobs-reference .jobs-actions { display:flex; gap:10px; flex-wrap:wrap; }
        .jobs-reference .jobs-button { display:inline-flex; align-items:center; gap:8px; min-height:42px; padding:10px 15px; border-radius:10px; font-size:.76rem; font-weight:800; text-decoration:none; transition:.2s; }
        .jobs-reference .jobs-button:hover { transform:translateY(-2px); }
        .jobs-reference .jobs-button.export { color:#047857; background:#ecfdf5; border:1px solid #a7f3d0; }
        .jobs-reference .jobs-button.create { color:#fff; background:#173b78; box-shadow:0 7px 16px rgba(23,59,120,.16); }
        .jobs-reference .filter-panel { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:22px; padding:16px; border:1px solid #e5ebf3; border-radius:14px; background:#fff; box-shadow:0 6px 22px rgba(15,23,42,.045); }
        .jobs-reference .filter-tabs { display:flex; gap:6px; flex-wrap:wrap; }
        .jobs-reference .filter-tab { padding:9px 13px; border-radius:99px; color:#64748b; background:#f8fafc; font-size:.74rem; font-weight:800; text-decoration:none; }
        .jobs-reference .filter-tab.active { color:#fff; background:#173b78; }
        .jobs-reference .filter-form { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .jobs-reference .search-box { display:flex; align-items:center; gap:8px; width:min(100%,300px); padding:9px 12px; border:1px solid #e2e8f0; border-radius:999px; background:#f8fafc; }
        .jobs-reference .search-box input { width:100%; border:0; outline:0; background:transparent; font-size:.75rem; }
        .jobs-reference .sort-box { padding:9px 12px; border:1px solid #e2e8f0; border-radius:999px; color:#475569; background:#f8fafc; font-size:.74rem; font-weight:700; }
        .jobs-reference .status-card { margin-bottom:22px; overflow:hidden; border:1px solid #e5ebf3; border-radius:16px; background:#fff; box-shadow:0 6px 22px rgba(15,23,42,.045); scroll-margin-top: 80px; }
        .jobs-reference .status-title { display:flex; align-items:center; gap:9px; padding:19px 22px; border-bottom:1px solid #eef2f7; color:#173b78; font-size:1rem; font-weight:900; }
        .jobs-reference .status-title.running { color:#2563eb; }
        .jobs-reference .status-title.done { color:#16a34a; }
        .jobs-reference .status-title.cancelled { color:#ef4444; }
        .jobs-reference .status-table-wrap { overflow-x:auto; }
        .jobs-reference .status-table { width:100%; min-width:1000px; border-collapse:collapse; text-align:left; }
        .jobs-reference .status-table th { padding:13px 18px; color:#94a3b8; border-bottom:2px solid #f1f5f9; font-size:.66rem; font-weight:900; letter-spacing:.05em; white-space:nowrap; }
        .jobs-reference .status-table td { padding:19px 18px; color:#475569; border-bottom:1px solid #f1f5f9; vertical-align:top; font-size:.76rem; }
        .jobs-reference .status-table tbody tr { transition:.2s; }
        .jobs-reference .status-table tbody tr:hover { background:#f8fbff; box-shadow:inset 3px 0 #f2b818; }
        .jobs-reference .job-name { display:inline; color:#173b78; font-size:.88rem; font-weight:900; line-height:1.45; overflow-wrap:anywhere; }
        .jobs-reference .schedule { display:grid; gap:5px; min-width:135px; color:#64748b; font-size:.7rem; line-height:1.35; }
        .jobs-reference .schedule b { color:#1e293b; }
        .jobs-reference .badge { display:inline-flex; width:max-content; padding:6px 10px; border-radius:999px; font-size:.68rem; font-weight:900; }
        .jobs-reference .badge.running { color:#2563eb; background:#eff6ff; border:1px solid #bfdbfe; }
        .jobs-reference .badge.done { color:#16a34a; background:#f0fdf4; border:1px solid #bbf7d0; }
        .jobs-reference .badge.cancelled { color:#ef4444; background:#fef2f2; border:1px solid #fecaca; }
        .jobs-reference .actions { display:flex; gap:6px; align-items:center; white-space:nowrap; }
        .jobs-reference .icon-action { display:inline-grid; width:36px; height:36px; place-items:center; border:0; border-radius:999px; transition:.2s; cursor:pointer; }
        .jobs-reference .icon-action:hover { transform:translateY(-2px); }
        .jobs-reference .icon-action.view { color:#2563eb; background:#eff6ff; }
        .jobs-reference .icon-action.edit { color:#d97706; background:#fffbeb; }
        .jobs-reference .icon-action.cancel { color:#64748b; background:#f1f5f9; }
        .jobs-reference .icon-action.restore { color:#16a34a; background:#f0fdf4; }
        .jobs-reference .icon-action.delete { color:#ef4444; background:#fef2f2; }
        .jobs-reference .empty { padding:34px 18px !important; color:#94a3b8 !important; text-align:center; font-size:.8rem !important; }
        @media (max-width:700px) { .jobs-reference .jobs-head { align-items:flex-start; flex-direction:column; } .jobs-reference .jobs-actions, .jobs-reference .jobs-button { width:100%; justify-content:center; } .jobs-reference .filter-form { width:100%; } .jobs-reference .search-box { flex:1; width:auto; } }
    </style>

    <div class="jobs-reference">
        <!-- Header -->
        <div class="jobs-head">
            <div>
                <h1>Job Package</h1>
                <p>Daftar seluruh pekerjaan Jasa Pelayanan Pabrik berdasarkan status.</p>
            </div>
            <div class="jobs-actions">
                <a class="jobs-button export" href="{{ route('admin.job-packages.export') }}">↓ Export Excel</a>
                <a class="jobs-button create" href="{{ route('admin.job-packages.create') }}">＋ Tambah Job Package</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Panel -->
        @php $currentStatusFilter = request('status_filter', 'semua'); @endphp
        <div class="filter-panel">
            <div class="filter-tabs">
                @foreach(['semua' => 'Semua', 'aktif' => 'Aktif', 'batal' => 'Tidak Aktif'] as $value => $label)
                    <a class="filter-tab {{ $currentStatusFilter === $value ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status_filter' => $value]) }}#table-section">{{ $label }}</a>
                @endforeach
            </div>
            <form class="filter-form" method="GET" action="{{ route('admin.job-packages.index') }}#table-section">
                <input type="hidden" name="status_filter" value="{{ $currentStatusFilter }}">
                <div class="search-box">
                    <span class="text-slate-400">⌕</span>
                    <input name="search" value="{{ request('search') }}" placeholder="Cari Job Package, SO, PO...">
                </div>
                <select class="sort-box" name="sort" onchange="this.form.submit()">
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

        @php
            $boards = [
                ['id' => 'sedang-berjalan', 'class' => 'running', 'title' => 'Job Package: Sedang Berjalan', 'items' => $runningJobs, 'empty' => 'Tidak ada pekerjaan yang sedang berjalan.'],
                ['id' => 'selesai', 'class' => 'done', 'title' => 'Job Package: Selesai 100%', 'items' => $doneJobs, 'empty' => 'Belum ada pekerjaan yang selesai.'],
                ['id' => 'batal', 'class' => 'cancelled', 'title' => 'Job Package: Tidak Dilanjutkan', 'items' => $cancelledJobs, 'empty' => 'Tidak ada pekerjaan yang dibatalkan.'],
            ];

            if ($currentStatusFilter === 'aktif') {
                $boards = array_filter($boards, fn($b) => in_array($b['id'], ['sedang-berjalan', 'selesai']));
            } elseif ($currentStatusFilter === 'batal') {
                $boards = array_filter($boards, fn($b) => $b['id'] === 'batal');
            }
        @endphp

        <!-- Perulangan untuk setiap Tabel Status -->
        @foreach($boards as $board)
            <section id="{{ $board['id'] }}" class="status-card">
                <h2 class="status-title {{ $board['class'] }}">
                    <span>{{ $board['class'] === 'running' ? '◷' : ($board['class'] === 'done' ? '✓' : '×') }}</span>
                    {{ $board['title'] }}
                </h2>
                <div class="status-table-wrap">
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th class="w-[5%]">NO</th>
                                <th class="w-[35%]">JOB PACKAGE & SURAT</th>
                                <th class="w-[22%]">REFERENSI</th>
                                <th class="w-[18%]">PIC & JADWAL</th>
                                <th class="w-[12%]">STATUS & PROGRES</th>
                                <th class="w-[8%] text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($board['items'] as $jp)
                                @php
                                    $progress = max(0, min(100, (float) $jp->hasil_progres));
                                    $isCancelled = $jp->status === 'batal';

                                    // 1. Ekstraksi Nomor Surat / BAK
                                    $suratDocsList = [];
                                    if (isset($jp->suratBakDocs) && $jp->suratBakDocs->count() > 0) {
                                        foreach ($jp->suratBakDocs as $sDoc) {
                                            $val = $sDoc->no_surat ?? $sDoc->no_surat_bak_doc ?? $sDoc->nomor_surat ?? null;
                                            if (!empty($val)) {
                                                $suratDocsList[] = $val;
                                            }
                                        }
                                    }
                                    if (empty($suratDocsList)) {
                                        $rawSurat = $jp->no_surat_bak_doc ?? $jp->no_surat ?? null;
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

                                    // 2. Ekstraksi PO & Harga
                                    $pos = $jp->pos;
                                    $firstPo = $pos ? $pos->first() : null;
                                    $extraPoCount = $pos ? ($pos->count() - 1) : 0;

                                    $getPoNumber = function($poItem) {
                                        if (is_object($poItem)) {
                                            return $poItem->no_po ?? $poItem->po_number ?? $poItem->nomor_po ?? $poItem->no_po_doc ?? '-';
                                        }
                                        if (is_array($poItem)) {
                                            return $poItem['no_po'] ?? $poItem['po_number'] ?? $poItem['nomor_po'] ?? $poItem['no_po_doc'] ?? '-';
                                        }
                                        return is_scalar($poItem) ? (string)$poItem : '-';
                                    };

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
                                    $deptList = $jp->permintaanDaris ? $jp->permintaanDaris->pluck('permintaan_dari')->filter()->join(', ') : '';
                                    if (empty($deptList)) {
                                        $deptList = $jp->departemen ?? '-';
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $board['items']->firstItem() + $loop->index }}</strong></td>
                                    
                                    <!-- JOB PACKAGE & SURAT -->
                                    <td>
                                        <div class="flex items-start gap-2 flex-wrap">
                                            <a class="job-name hover:text-blue-700 transition-colors" href="{{ route('admin.job-packages.show', $jp) }}">
                                                {{ $jp->job_package }}
                                            </a>

                                            @if(($jp->visibility ?? 'public') === 'internal')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200 shrink-0">
                                                    🔒 Privat (INT JPP)
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2 space-y-1.5 text-[11px] text-slate-500">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-slate-400 font-semibold">No. Surat / BAK:</span>
                                                @if(count($suratDocsList) > 0)
                                                    <div class="flex flex-col space-y-1 w-full">
                                                        @foreach($suratDocsList as $sNum)
                                                            <div class="text-[11px] font-bold text-blue-700 bg-blue-50/80 border border-blue-200/80 px-2 py-0.5 rounded-md w-fit leading-tight break-all">
                                                                {{ $sNum }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 font-semibold">-</span>
                                                @endif
                                            </div>

                                            <p><span class="text-slate-400">Departemen:</span> <span class="text-slate-700 font-medium">{{ $deptList }}</span></p>
                                        </div>
                                    </td>

                                    <!-- REFERENSI -->
                                    <td>
                                        <div class="space-y-1 text-slate-500 font-medium text-[11px]">
                                            <div>
                                                <span class="text-slate-400">No. SO:</span> 
                                                <strong class="text-slate-800">{{ $jp->no_service_order ?: '-' }}</strong>
                                            </div>

                                            <div class="flex items-center gap-1.5 flex-wrap" x-data="{ openPO: false }">
                                                <span class="text-slate-400">No. PO:</span>
                                                
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

                                                            <div x-show="openPO" 
                                                                 x-transition:enter="transition ease-out duration-150"
                                                                 x-transition:enter-start="opacity-0 scale-95"
                                                                 x-transition:enter-end="opacity-100 scale-100"
                                                                 x-transition:leave="transition ease-in duration-100"
                                                                 x-transition:leave-start="opacity-100 scale-100"
                                                                 x-transition:leave-end="opacity-0 scale-95"
                                                                 class="absolute left-0 mt-1 w-60 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5 z-30" 
                                                                 style="display: none;">
                                                                
                                                                <div class="flex items-center justify-between mb-2 pb-1 border-b border-slate-100">
                                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Daftar No. PO & Harga</span>
                                                                    <span class="text-[10px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded-full font-bold border border-amber-200/60">{{ $pos->count() }} Total</span>
                                                                </div>

                                                                <ul class="space-y-1 max-h-44 overflow-y-auto pr-1">
                                                                    @foreach($pos as $poItem)
                                                                        @php
                                                                            $poNum = $getPoNumber($poItem);
                                                                            $poPrice = $getPoPrice($poItem);
                                                                        @endphp
                                                                        <li class="flex items-center justify-between gap-2 text-[11px] font-semibold text-slate-700 bg-slate-50 px-2 py-1 rounded-md border border-slate-100 hover:bg-slate-100 transition-colors">
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
                                                @elseif(!empty($jp->no_po))
                                                    <span class="font-bold text-slate-800">{{ $jp->no_po }}</span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </div>

                                            <div>
                                                <span class="text-slate-400">Notif:</span> 
                                                <span class="text-slate-700 font-medium">{{ $jp->no_service_notifikasi ?? '-' }}</span>
                                            </div>

                                            <div>
                                                <span class="text-slate-400">Estimasi OE:</span> 
                                                <strong class="text-slate-800">Rp {{ number_format($jp->owner_estimate ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- PIC & JADWAL -->
                                    <td>
                                        <div class="schedule">
                                            <b>{{ $jp->creator->name ?? 'Administrator JPP' }}</b>
                                            <span>▶ Mulai: <b>{{ optional($jp->tanggal_mulai_pekerjaan)->format('d M Y') ?: '-' }}</b></span>
                                            <span>⚑ Target: <b>{{ optional($jp->tanggal_selesai_pekerjaan)->format('d M Y') ?: '-' }}</b></span>
                                        </div>
                                    </td>

                                    <!-- STATUS & PROGRES -->
                                    <td>
                                        <span class="badge {{ $board['class'] }}">
                                            {{ $isCancelled ? 'Dibatalkan' : ($progress >= 100 ? 'Selesai 100%' : 'Sedang Berjalan') }}
                                        </span>
                                        <div class="mt-2 w-32">
                                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-500 mb-1">
                                                <span>PROGRES</span>
                                                <span class="text-blue-900 font-black">{{ number_format($progress, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full {{ $board['class'] === 'done' ? 'bg-emerald-500' : ($board['class'] === 'cancelled' ? 'bg-rose-500' : 'bg-blue-600') }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="text-center">
                                        <div class="actions justify-center">
                                            <a class="icon-action view" title="Detail" href="{{ route('admin.job-packages.show', $jp) }}">◉</a>
                                            <a class="icon-action edit" title="Edit" href="{{ route('admin.job-packages.edit', $jp) }}">✎</a>
                                            
                                            @if($isCancelled)
                                                <form method="POST" action="{{ route('admin.job-packages.reactivate', $jp) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="icon-action restore" title="Aktifkan kembali">↻</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.job-packages.cancel', $jp) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="icon-action cancel" title="Batalkan">⊘</button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('admin.job-packages.destroy', $jp) }}" onsubmit="return confirm('Hapus Job Package ini secara permanen?')">
                                                    @csrf @method('DELETE')
                                                    <button class="icon-action delete" title="Hapus">⌫</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty">{{ $board['empty'] }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Khusus Per Tabel -->
                @if($board['items']->hasPages())
                    <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 font-medium">
                            Menampilkan <span class="font-bold text-slate-700">{{ $board['items']->firstItem() }}</span> - <span class="font-bold text-slate-700">{{ $board['items']->lastItem() }}</span> dari <span class="font-bold text-slate-700">{{ $board['items']->total() }}</span> data
                        </div>
                        
                        <nav role="navigation" aria-label="Pagination Navigation" class="inline-flex items-center rounded-lg border border-amber-400 bg-white overflow-hidden shadow-sm">
                            {{-- Previous Page Link --}}
                            @if ($board['items']->onFirstPage())
                                <span class="px-3.5 py-2 text-xs font-bold text-amber-300 cursor-not-allowed bg-slate-50">«</span>
                            @else
                                <a href="{{ $board['items']->previousPageUrl() }}#{{ $board['id'] }}" class="px-3.5 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 transition-colors">«</a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($board['items']->getUrlRange(1, $board['items']->lastPage()) as $page => $url)
                                @if ($page == $board['items']->currentPage())
                                    <span class="px-4 py-2 text-xs font-extrabold bg-amber-500 text-white border-l border-amber-200">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}#{{ $board['id'] }}" class="px-4 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 border-l border-amber-200 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($board['items']->hasMorePages())
                                <a href="{{ $board['items']->nextPageUrl() }}#{{ $board['id'] }}" class="px-3.5 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50 border-l border-amber-200 transition-colors">»</a>
                            @else
                                <span class="px-3.5 py-2 text-xs font-bold text-amber-300 cursor-not-allowed bg-slate-50 border-l border-amber-200">»</span>
                            @endif
                        </nav>
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-admin-layout>