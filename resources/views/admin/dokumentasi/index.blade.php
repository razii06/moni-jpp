<x-admin-layout title="Dokumentasi Beranda">
    {{-- CSS Khusus untuk menyembunyikan modal sebelum Alpine JS dimuat --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="space-y-6" x-data="{ 
        open: false, 
        category: '', 
        visibilitas: 'publik',
        fileName: '', 
        fileSize: '',
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = file.size >= 1024 * 1024 
                    ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                    : (file.size / 1024).toFixed(2) + ' KB';
            }
        },
        resetForm() {
            this.open = false;
            this.category = '';
            this.visibilitas = 'publik';
            this.fileName = '';
            this.fileSize = '';
        }
    }">
        
        {{-- Card Utama --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            
            {{-- Header Card & Tombol Tambah --}}
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between animate-pop-up">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 shadow-sm border border-blue-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Daftar Media</h1>
                        <p class="text-xs text-slate-500">Kelola foto kegiatan, dokumen PDF, dan tautan video beranda.</p>
                    </div>
                </div>

                <button type="button" @click="open = true" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#0f2b5c] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-950/10 transition hover:bg-blue-900 active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Upload Dokumentasi
                </button>
            </div>

            {{-- Filter Tab & Search Bar --}}
            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between animate-pop-up delay-100">
                {{-- Tab Filter (Segmented Control) --}}
                <div class="inline-flex items-center gap-1 rounded-2xl bg-slate-100/80 p-1.5 border border-slate-200/60 w-fit">
                    <a href="{{ route('admin.dokumentasi.index') }}" 
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all {{ !request('tab') ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Semua Media
                    </a>
                    <a href="{{ route('admin.dokumentasi.index', ['tab' => 'foto_video']) }}" 
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all {{ request('tab') === 'foto_video' ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Foto & Video
                    </a>
                    <a href="{{ route('admin.dokumentasi.index', ['tab' => 'pdf']) }}" 
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all {{ request('tab') === 'pdf' ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Dokumen PDF
                    </a>
                </div>

                {{-- Input Pencarian --}}
                <form method="GET" action="{{ route('admin.dokumentasi.index') }}" class="relative w-full lg:w-80">
                    @if(request('tab'))
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama media..." 
                        style="padding-left: 3.25rem !important;" 
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none transition">
                </form>
            </div>

            {{-- Tabel Media --}}
            <div class="overflow-x-auto rounded-2xl border border-slate-100 animate-pop-up delay-200">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-4">PREVIEW MEDIA</th>
                            <th class="py-3.5 px-4">KATEGORI</th>
                            <th class="py-3.5 px-4">STATUS TAMPIL</th>
                            <th class="py-3.5 px-4">TANGGAL UPLOAD</th>
                            <th class="py-3.5 px-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dokumentasi as $item)
                            @php
                                $rawPath = $item->file_path;
                                $fileUrl = $rawPath;
                                $previewUrl = $rawPath;

                                if (\Illuminate\Support\Str::startsWith($rawPath, ['http://', 'https://'])) {
                                    if (\Illuminate\Support\Str::contains($rawPath, ['drive.google.com', 'googleusercontent.com'])) {
                                        preg_match('/[-\w]{25,}/', $rawPath, $matches);
                                        $fileId = $matches[0] ?? null;
                                        if ($fileId) {
                                            $previewUrl = "https://lh3.googleusercontent.com/d/{$fileId}";
                                            $fileUrl = "https://drive.google.com/file/d/{$fileId}/view";
                                        }
                                    }
                                } else {
                                    $previewUrl = \Illuminate\Support\Str::startsWith($rawPath, 'storage/') 
                                        ? asset($rawPath) 
                                        : asset('storage/' . ltrim($rawPath, '/'));
                                    $fileUrl = $previewUrl;
                                }
                            @endphp
                            <tr class="transition hover:bg-slate-50/60">
                                {{-- Preview Media --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200/60 bg-slate-100">
                                            @if($item->kategori === 'Foto Kegiatan')
                                                <img src="{{ $previewUrl }}" alt="{{ $item->judul }}" class="h-full w-full object-cover">
                                            @elseif($item->kategori === 'Dokumen PDF')
                                                <div class="flex flex-col items-center justify-center text-rose-500">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.5L14.5 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    <span class="text-[8px] font-black tracking-tight">PDF</span>
                                                </div>
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-blue-50 text-blue-600">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 line-clamp-1">{{ $item->judul }}</p>
                                            <p class="text-[11px] font-medium text-slate-400">Ukuran: {{ $item->ukuran_file ?: 'Tautan' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori Badge --}}
                                <td class="py-3.5 px-4">
                                    @if($item->kategori === 'Foto Kegiatan')
                                        <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 border border-blue-100">
                                            {{ $item->kategori }}
                                        </span>
                                    @elseif($item->kategori === 'Dokumen PDF')
                                        <span class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 border border-rose-100">
                                            {{ $item->kategori }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 border border-indigo-100">
                                            {{ $item->kategori }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Tampil Badge --}}
                                <td class="py-3.5 px-4">
                                    @if($item->visibilitas === 'publik')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600 border border-emerald-100">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Publik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500 border border-slate-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Privat
                                        </span>
                                    @endif
                                </td>

                                {{-- Tanggal Upload --}}
                                <td class="py-3.5 px-4 text-xs font-semibold text-slate-500">
                                    {{ $item->created_at?->format('d M Y') }}
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ $fileUrl }}" 
                                            target="_blank" rel="noopener" 
                                            class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600 transition hover:bg-emerald-100 border border-emerald-100">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat
                                        </a>

                                        {{-- Tombol Hapus Dibatasi Menggunakan Gate Otorisasi delete-data --}}
                                        @can('delete-data')
                                            <form action="{{ route('admin.dokumentasi.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus dokumentasi ini?')">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-500 transition hover:bg-rose-100 border border-rose-100">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-8 w-8 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                        </div>
                                        <p class="text-base font-bold text-slate-700">Belum ada dokumentasi tersimpan</p>
                                        <p class="text-xs text-slate-400 mt-1">Unggah file atau masukkan tautan video baru untuk menambahkan media.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Modal Popup Upload Dokumentasi (Teleport ke body agar blur menutupi full layar) --}}
        <template x-teleport="body">
            <div x-show="open" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-md" @keydown.escape.window="resetForm()">
                <div @click.outside="resetForm()" x-transition class="w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl sm:p-8 border border-slate-100 relative">
                    <div class="mb-6 flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Upload Dokumentasi</h2>
                            <p class="mt-1 text-xs text-slate-500">Pilih kategori dan unggah file berkas ke penyimpanan aplikasi.</p>
                        </div>
                        <button type="button" @click="resetForm()" class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition">&times;</button>
                    </div>

                    <form action="{{ route('admin.dokumentasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        {{-- Judul Dokumentasi --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Judul Dokumentasi</label>
                            <input name="judul" required maxlength="255" class="w-full rounded-2xl border border-slate-200 py-2.5 px-4 text-xs font-medium focus:border-blue-500 focus:ring-blue-500 transition" placeholder="Contoh: Inspeksi Boiler Unit 2">
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Kategori</label>
                            <select name="kategori" x-model="category" required class="w-full rounded-2xl border border-slate-200 py-2.5 px-3 text-xs font-medium focus:border-blue-500 focus:ring-blue-500 transition">
                                <option value="">Pilih kategori</option>
                                <option value="Foto Kegiatan">Foto Kegiatan</option>
                                <option value="Dokumen PDF">Dokumen PDF</option>
                                <option value="Tautan Video">Tautan Video</option>
                            </select>
                        </div>

                        {{-- Visibilitas / Hak Akses (Radio Cards) --}}
                        <div>
                            <label class="mb-2 block text-xs font-bold text-slate-700">Visibilitas / Hak Akses</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                
                                {{-- Opsi 1: Reguler (Publik) --}}
                                <label @click="visibilitas = 'publik'" 
                                       :class="visibilitas === 'publik' ? 'border-blue-500 bg-blue-50/50 ring-2 ring-blue-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'"
                                       class="relative flex flex-col p-3.5 border rounded-2xl cursor-pointer transition-all duration-200">
                                    <input type="radio" name="visibilitas" value="publik" x-model="visibilitas" class="sr-only">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                            <path stroke-width="2" stroke-linecap="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z"/>
                                        </svg>
                                        <span class="font-extrabold text-xs text-blue-600">Reguler (Publik)</span>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 leading-snug">Bisa dilihat oleh Tamu di halaman awal.</p>
                                </label>

                                {{-- Opsi 2: Khusus INT JPP (Privat) --}}
                                <label @click="visibilitas = 'privat'"
                                       :class="visibilitas === 'privat' ? 'border-red-400 bg-red-50/40 ring-2 ring-red-400/20' : 'border-slate-200 bg-white hover:bg-slate-50'"
                                       class="relative flex flex-col p-3.5 border rounded-2xl cursor-pointer transition-all duration-200">
                                    <input type="radio" name="visibilitas" value="privat" x-model="visibilitas" class="sr-only">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="5" y="11" width="14" height="10" rx="2" stroke-width="2"/>
                                            <path d="M8 11V7a4 4 0 018 0v4" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="font-extrabold text-xs text-red-500">Khusus INT JPP (Privat)</span>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 leading-snug">Izin Prinsip. Hanya tampil di Dashboard Admin.</p>
                                </label>

                            </div>
                        </div>

                        {{-- Form Tautan Video --}}
                        <div x-show="category === 'Tautan Video'">
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">URL Video (YouTube / Google Drive)</label>
                            <input type="url" name="tautan_video" :disabled="category !== 'Tautan Video'" class="w-full rounded-2xl border border-slate-200 py-2.5 px-4 text-xs font-medium focus:border-blue-500 focus:ring-blue-500 transition" placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        {{-- Form File Upload --}}
                        <div x-show="category !== 'Tautan Video'">
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Berkas File (JPG, PNG, WEBP, PDF)</label>
                            <div class="group relative flex min-h-[120px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-4 transition hover:border-blue-400 bg-slate-50/50">
                                
                                <input type="file" name="file_upload" :disabled="category === 'Tautan Video'" accept="image/jpeg,image/png,image/webp,application/pdf" @change="handleFileChange($event)" class="absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer">

                                {{-- Tampilan Default (Belum Ada File) --}}
                                <template x-if="!fileName">
                                    <div class="flex flex-col items-center justify-center text-center pointer-events-none">
                                        <svg class="h-8 w-8 text-slate-400 mb-1 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-xs font-semibold text-slate-600">Klik atau seret file ke area ini</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Maksimal ukuran file: 10 MB</p>
                                    </div>
                                </template>

                                {{-- Tampilan Ketika File Dipilih --}}
                                <template x-if="fileName">
                                    <div class="flex items-center gap-3 w-full bg-blue-50/90 border border-blue-200 p-3 rounded-xl z-20">
                                        <div class="p-2 bg-blue-600 text-white rounded-lg shrink-0">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="text-left overflow-hidden min-w-0 flex-1">
                                            <p x-text="fileName" class="text-xs font-bold text-slate-800 truncate"></p>
                                            <p x-text="fileSize" class="text-[10px] text-slate-500"></p>
                                        </div>
                                        <span class="text-xs font-bold text-blue-600 shrink-0">Terpilih ✓</span>
                                    </div>
                                </template>

                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="resetForm()" class="rounded-2xl px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                            <button type="submit" class="rounded-2xl bg-[#0f2b5c] px-6 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-950/10 hover:bg-blue-900 transition active:scale-95">Simpan Dokumentasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-admin-layout>