<x-admin-layout title="Edit Job Package">
    <!-- Header Section -->
    <div class="mb-6">
        <a href="{{ route('admin.job-packages.show', $jobPackage) }}"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-[#0f2b5c] transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Edit Job Package</h2>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                    Memperbarui data:
                    @php
                        $displayJudul = is_string($jobPackage->job_package) && is_array(json_decode($jobPackage->job_package, true)) 
                            ? (json_decode($jobPackage->job_package, true)[0] ?? 'Judul') 
                            : (is_array($jobPackage->job_package) ? ($jobPackage->job_package[0] ?? 'Judul') : $jobPackage->job_package);
                    @endphp
                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                        {{ $displayJudul }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Main Form Submit dengan ID form-edit dan multipart/form-data -->
    <form id="form-edit" method="POST" action="{{ route('admin.job-packages.update', $jobPackage) }}" enctype="multipart/form-data" class="space-y-6 max-w-5xl">
        @csrf
        @method('PUT')

        <!-- Visibilitas / Hak Akses -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-3">
            <label class="block text-xs font-bold uppercase text-slate-600">
                Visibilitas / Hak Akses <span class="text-rose-500">*</span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Opsi 1: Reguler (Publik) -->
                <div>
                    <input type="radio" 
                        name="visibility" 
                        id="visibility_public" 
                        value="public" 
                        class="peer hidden" 
                        {{ old('visibility', $jobPackage->visibility ?? 'public') === 'public' ? 'checked' : '' }}>
                    
                    <label for="visibility_public" 
                        class="flex flex-col p-3.5 rounded-2xl border border-slate-200 bg-white cursor-pointer transition-all hover:border-slate-300 peer-checked:border-blue-500 peer-checked:bg-blue-50/40 peer-checked:ring-1 peer-checked:ring-blue-500">
                        <div class="flex items-center gap-2 font-bold text-blue-600 text-sm mb-1">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10z"/>
                            </svg>
                            <span>Reguler (Publik)</span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Bisa dilihat oleh Tamu di halaman awal.
                        </p>
                    </label>
                </div>

                <!-- Opsi 2: Khusus INT JPP (Privat) -->
                <div>
                    <input type="radio" 
                        name="visibility" 
                        id="visibility_internal" 
                        value="internal" 
                        class="peer hidden" 
                        {{ old('visibility', $jobPackage->visibility ?? '') === 'internal' ? 'checked' : '' }}>
                    
                    <label for="visibility_internal" 
                        class="flex flex-col p-3.5 rounded-2xl border border-slate-200 bg-white cursor-pointer transition-all hover:border-slate-300 peer-checked:border-red-500 peer-checked:bg-red-50/40 peer-checked:ring-1 peer-checked:ring-red-500">
                        <div class="flex items-center gap-2 font-bold text-red-600 text-sm mb-1">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>Khusus INT JPP (Privat)</span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Izin Prinsip. Hanya tampil di Dashboard Admin.
                        </p>
                    </label>
                </div>
            </div>

            @error('visibility')
                <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Section A: Identitas Job Package -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-[#0f2b5c] rounded-full"></span>
                A. Identitas Job Package
            </h3>

            <!-- 1. Dynamic Repeater Judul Barang/Jasa -->
            <div class="pb-3 border-b border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">
                            Judul / Nama Barang & Jasa <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] font-normal text-slate-400 block mt-0.5">Tambahkan lebih dari satu judul jika terdapat beberapa barang/jasa</span>
                    </div>
                    <button type="button" id="add-judul-row"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-[#0f2b5c] rounded-xl text-xs font-bold hover:bg-blue-100 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Judul
                    </button>
                </div>

                <div class="space-y-2" id="judul-container">
                    @php
                        $existingJuduls = is_string($jobPackage->job_package ?? null) && is_array(json_decode($jobPackage->job_package ?? '[]', true)) 
                            ? json_decode($jobPackage->job_package, true) 
                            : (is_array($jobPackage->job_package ?? null) ? $jobPackage->job_package : [$jobPackage->job_package ?? '']);
                        $oldJuduls = old('job_package', $existingJuduls);
                    @endphp
                    @foreach($oldJuduls as $index => $judul)
                        <div class="judul-row flex gap-2 items-center">
                            <span class="row-number shrink-0 w-7 h-9 flex items-center justify-center text-[11px] font-bold text-slate-400">-</span>
                            <input type="text" name="job_package[]" value="{{ $judul }}" required
                                placeholder="Contoh: Jasa Fabrikasi Roller : Return Roller BW1200 (100ea)"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all">
                            <button type="button" class="remove-judul-row shrink-0 p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                @error('job_package')
                    <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- 2. Periode Penyewaan -->
            <div class="pt-2 pb-3 border-b border-slate-100">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Periode<span class="text-rose-500">*</span>
                    <span class="text-[10px] font-normal text-slate-400 block mt-0.5">Isi dengan rentang waktu penyewaan atau gunakan tanda "-" jika tidak ada</span>
                </label>
                <input type="text" name="periode" value="{{ old('periode', $jobPackage->periode ?? '-') }}" required
                    placeholder="Contoh: April - Juni 2026 atau -"
                    class="w-full px-4 py-2.5 bg-slate-50 border @error('periode') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all">
                @error('periode')
                    <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- 3. Dynamic Repeater: No. Dokumen -->
            <div class="pt-2 pb-3 border-b border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700">
                        No. Dokumen <span class="text-rose-500">*</span>
                        <span class="text-[10px] font-normal text-slate-400 block mt-0.5">Item pertama wajib diisi, tambahkan lebih jika ada beberapa dokumen</span>
                    </label>
                    <button type="button" id="add-surat-row"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-[#0f2b5c] rounded-xl text-xs font-bold hover:bg-blue-100 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </button>
                </div>

                <div class="space-y-2" id="surat-container">
                    @php
                        $existingSurat = isset($jobPackage) && $jobPackage->suratBakDocs ? $jobPackage->suratBakDocs->pluck('no_surat_bak_doc')->toArray() : [];
                        if (empty($existingSurat)) {
                            $existingSurat = is_string($jobPackage->surat_bak_docs ?? null) ? json_decode($jobPackage->surat_bak_docs, true) ?? [$jobPackage->surat_bak_docs] : ($jobPackage->surat_bak_docs ?? ['']);
                        }
                        $oldSurat = old('surat_bak_docs', $existingSurat);
                    @endphp
                    @foreach($oldSurat as $index => $surat)
                        <div class="surat-row flex gap-2 items-start">
                            <span class="shrink-0 w-7 h-9 flex items-center justify-center text-[11px] font-bold text-slate-400">-</span>
                            <textarea name="surat_bak_docs[{{ $index }}]" rows="2" required
                                placeholder="Contoh: 05570/E/TK/3310/IT/2026 019.BAK/INT.JPP/PIM/III/2026"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all resize-none">{{ $surat }}</textarea>
                            <button type="button"
                                class="remove-surat-row shrink-0 p-2 mt-0.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                @error('surat_bak_docs')
                    <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- 4. Dynamic Repeater: Permintaan Dari -->
            <div class="pt-2 pb-3 border-b border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700">
                        Permintaan Dari
                        <span class="text-[10px] font-normal text-slate-400 block mt-0.5">Departemen internal / instansi eksternal pemohon. Boleh kosong.</span>
                    </label>
                    <button type="button" id="add-permintaan-row"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </button>
                </div>

                <div class="space-y-2" id="permintaan-container">
                    @php
                        $existingPermintaan = isset($jobPackage) && $jobPackage->permintaanDaris ? $jobPackage->permintaanDaris->pluck('permintaan_dari')->toArray() : [];
                        if (empty($existingPermintaan)) {
                            $existingPermintaan = is_string($jobPackage->permintaan_dari ?? null) ? json_decode($jobPackage->permintaan_dari, true) ?? [$jobPackage->permintaan_dari] : ($jobPackage->permintaan_dari ?? ['']);
                        }
                        $oldPermintaan = old('permintaan_dari', $existingPermintaan);
                    @endphp
                    @foreach($oldPermintaan as $index => $permintaan)
                        <div class="permintaan-row flex gap-2 items-start">
                            <span class="shrink-0 w-7 h-9 flex items-center justify-center text-[11px] font-bold text-slate-400">-</span>
                            <input type="text" name="permintaan_dari[{{ $index }}]" value="{{ $permintaan }}"
                                placeholder="Contoh: Dept. Rendal HAR"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all">
                            <button type="button"
                                class="remove-permintaan-row shrink-0 p-2 mt-0.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                @error('permintaan_dari')
                    <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- 5. Field Sisanya (Tanggal Masuk, Notifikasi, dll) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Surat Masuk</label>
                    <input type="date" name="tanggal_surat_masuk" 
                        value="{{ old('tanggal_surat_masuk', optional($jobPackage->tanggal_surat_masuk ?? null)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border @error('tanggal_surat_masuk') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all">
                    @error('tanggal_surat_masuk')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        No. Service Notifikasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_service_notifikasi" value="{{ old('no_service_notifikasi', $jobPackage->no_service_notifikasi ?? '') }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border @error('no_service_notifikasi') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all">
                    @error('no_service_notifikasi')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        No. Service Order (SO) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_service_order" value="{{ old('no_service_order', $jobPackage->no_service_order ?? '') }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border @error('no_service_order') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all">
                    @error('no_service_order')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- PERBAIKAN 1: Mengubah type="number" menjadi type="text" agar tidak terhalang validasi HTML5 saat menggunakan format ribuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Owner Estimate (OE) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="owner_estimate" value="{{ old('owner_estimate', $jobPackage->owner_estimate ?? '') }}" required
                        placeholder="Contoh: 687.350.000"
                        class="w-full px-4 py-2.5 bg-slate-50 border @error('owner_estimate') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all">
                    @error('owner_estimate')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- PERBAIKAN 1: Mengubah type="number" menjadi type="text" -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Final Harga <span class="text-[10px] font-normal text-slate-400">(Otomatis kalkulasi dari PO)</span>
                    </label>
                    <input type="text" id="final_harga" name="final_harga" value="{{ old('final_harga', $jobPackage->final_harga ?? '') }}"
                        placeholder="0"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] transition-all">
                </div>
            </div>
        </div>

        <!-- Section Dynamic PO Items -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full"></span>
                        Detail Purchase Order (PO)
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Tambahkan satu atau beberapa data No. PO & rincian harganya.</p>
                </div>
                <button type="button" id="add-po-row"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah PO
                </button>
            </div>

            <div class="space-y-3" id="po-container">
                @php
                    $posRelation = $jobPackage->pos ?? $jobPackage->purchaseOrders ?? collect();
                    $existingPos = [];

                    if ($posRelation->count() > 0) {
                        $existingPos = $posRelation->toArray();
                    } else {
                        $existingPos = [
                            ['no_po' => $jobPackage->no_po ?? '', 'description' => '', 'price' => '']
                        ];
                    }
                    $oldPos = old('pos', $existingPos);
                @endphp

                @foreach($oldPos as $index => $po)
                    @php
                        $poArray = is_array($po) ? $po : $po->toArray();
                        $noPo = $poArray['no_po'] ?? '';
                        $desc = $poArray['description'] ?? $poArray['nama_item'] ?? $poArray['deskripsi'] ?? '';
                        $price = $poArray['price'] ?? $poArray['harga'] ?? '';
                    @endphp
                    <div class="po-row grid grid-cols-1 sm:grid-cols-12 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 items-end">
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">No. PO</label>
                            <input type="text" name="pos[{{ $index }}][no_po]" value="{{ $noPo }}"
                                placeholder="Contoh: PO-2026-001"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Item PO</label>
                            <input type="text" name="pos[{{ $index }}][description]" value="{{ $desc }}"
                                placeholder="Rincian / nama barang/jasa"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <!-- PERBAIKAN 1: Mengubah type="number" ke type="text" untuk harga PO -->
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Harga (Rp)</label>
                            <input type="text" name="pos[{{ $index }}][price]"
                                value="{{ $price }}" placeholder="0"
                                class="po-price-input w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <div class="sm:col-span-1 flex justify-end">
                            <button type="button"
                                class="remove-po-row p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Hapus Item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section B: Status, Progres & Periode Job Package -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                    B. Status, Progres & Periode Job Package
                </h3>
                <div class="px-3 py-1 bg-blue-50 border border-blue-200 rounded-lg text-xs font-bold text-blue-900 flex items-center gap-1.5">
                    <span>Total Progres:</span>
                    <span id="total-progress-display" class="text-sm text-blue-700 font-extrabold">0%</span>
                </div>
            </div>

            <!-- Periode Pekerjaan -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                <label class="block text-xs font-bold uppercase text-slate-700">
                    Periode Pekerjaan Keseluruhan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Mulai Pekerjaan</label>
                        <input type="date" name="tanggal_mulai_pekerjaan" 
                            value="{{ old('tanggal_mulai_pekerjaan', optional($jobPackage->tanggal_mulai_pekerjaan)->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        @error('tanggal_mulai_pekerjaan')
                            <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Selesai Pekerjaan</label>
                        <input type="date" name="tanggal_selesai_pekerjaan" 
                            value="{{ old('tanggal_selesai_pekerjaan', optional($jobPackage->tanggal_selesai_pekerjaan)->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        @error('tanggal_selesai_pekerjaan')
                            <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Komponen Bobot Progres -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">RAB LP-001/002 (%)</label>
                    <input type="text" name="rab_lp002" value="{{ old('rab_lp002', $jobPackage->rab_lp002) }}"
                        class="progress-input w-full px-4 py-2.5 bg-slate-50 border @error('rab_lp002') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all"
                        placeholder="0.00">
                    @error('rab_lp002')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">PB/J LP-001/002 (%)</label>
                    <input type="text" name="pbj_lp002" value="{{ old('pbj_lp002', $jobPackage->pbj_lp002) }}"
                        class="progress-input w-full px-4 py-2.5 bg-slate-50 border @error('pbj_lp002') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all"
                        placeholder="0.00">
                    @error('pbj_lp002')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Progress Pekerjaan (%)</label>
                    <input type="text" name="progress_pekerjaan" value="{{ old('progress_pekerjaan', $jobPackage->progress_pekerjaan) }}"
                        class="progress-input w-full px-4 py-2.5 bg-slate-50 border @error('progress_pekerjaan') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all"
                        placeholder="0.00">
                    @error('progress_pekerjaan')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">ADM Keuangan (%)</label>
                    <input type="text" name="proses_adm_keuangan" value="{{ old('proses_adm_keuangan', $jobPackage->proses_adm_keuangan) }}"
                        class="progress-input w-full px-4 py-2.5 bg-slate-50 border @error('proses_adm_keuangan') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all"
                        placeholder="0.00">
                    @error('proses_adm_keuangan')
                        <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Aktivitas Terkini</label>
                <textarea name="latest_activity" rows="2"
                    class="w-full px-3.5 py-2 bg-slate-50 border @error('latest_activity') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-slate-800 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 transition-all"
                    placeholder="Contoh: Pemasangan modul elektronik & wiring controller">{{ old('latest_activity', $jobPackage->latest_activity) }}</textarea>
                @error('latest_activity')
                    <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3"
                    class="w-full px-4 py-2.5 bg-slate-50 border @error('keterangan') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-[#0f2b5c] @enderror rounded-xl text-xs font-medium focus:ring-2 focus:bg-white transition-all">{{ old('keterangan', $jobPackage->keterangan) }}</textarea>
                @error('keterangan')
                    <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- C. DOKUMEN -->
        <div class="mb-8 border border-slate-200 rounded-2xl p-6 bg-white shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-800 mb-1 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                C. Dokumen (Opsional)
            </h3>
            <p class="text-[11px] text-slate-400 mb-5">Format didukung: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR. Anda dapat memilih beberapa file sekaligus.</p>

            @php
                $docsC = [
                    'doc_surat_permintaan' => '1. Surat Permintaan',
                    'doc_surat_izin_prinsip' => '2. Surat Izin Prinsip',
                    'doc_bak' => '3. BAK / Negosiasi',
                    'doc_rab' => '4. RAB (Rancangan Anggaran Biaya)',
                    'doc_form_pbj' => '5. Form Permintaan Barang & Jasa',
                    'doc_tor' => '6. TOR (Terms of Reference)',
                    'doc_bast' => '7. BAST (Berita Acara Serah Terima)',
                    'doc_laporan_pekerjaan' => '8. Laporan Pekerjaan',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($docsC as $field => $label)
                    <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100 flex flex-col justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">{{ $label }}</label>
                            @if ($jobPackage->$field)
                                <div class="flex items-center justify-between gap-2 mb-2 p-2 bg-emerald-50 rounded-lg border border-emerald-200 text-[11px]">
                                    <span class="text-emerald-700 font-semibold truncate">✓ Berkas tersimpan</span>
                                    <button type="button"
                                        onclick="if(confirm('Hapus dokumen ini?')) document.getElementById('delete-doc-{{ $field }}').submit();"
                                        class="text-rose-600 hover:underline font-bold shrink-0">
                                        Hapus
                                    </button>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="{{ $field }}[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error($field)
                            <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        <!-- D. DOKUMEN PENDUKUNG -->
        <div class="mb-8 border border-slate-200 rounded-2xl p-6 bg-white shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-800 mb-1 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                D. Dokumen Pendukung (Opsional)
            </h3>
            <p class="text-[11px] text-slate-400 mb-5">Lampirkan dokumen tambahan lainnya (bisa lebih dari satu). Format: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR.</p>
            
            @if ($jobPackage->doc_pendukung_lainnya)
                <div class="flex items-center justify-between gap-2 mb-3 p-2.5 bg-indigo-50 rounded-xl border border-indigo-200 text-xs max-w-xs">
                    <span class="text-indigo-700 font-semibold truncate">✓ Berkas pendukung tersimpan</span>
                    <button type="button"
                        onclick="if(confirm('Hapus dokumen pendukung ini?')) document.getElementById('delete-doc-doc_pendukung_lainnya').submit();"
                        class="text-rose-600 hover:underline font-bold shrink-0">
                        Hapus
                    </button>
                </div>
            @endif

            <div>
                <input type="file" name="doc_pendukung_lainnya[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('doc_pendukung_lainnya')
                    <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- PERBAIKAN 2 & 3: Tombol Submit dengan type="submit" dan dihubungkan secara eksplisit menggunakan atribut form="form-edit" -->
        <div class="flex items-center gap-3">
            <button type="submit" form="form-edit"
                class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-6 py-3 rounded-xl font-bold text-xs transition-all shadow-md cursor-pointer">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.job-packages.show', $jobPackage) }}"
                class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-xs transition-all">
                Batal
            </a>
        </div>
    </form>

    <!-- Form Tersembunyi untuk Hapus Berkas Single/Multiple -->
    @php
        $allDocs = array_merge(array_keys($docsC), ['doc_pendukung_lainnya']);
    @endphp
    @foreach ($allDocs as $field)
        @if ($jobPackage->$field)
            <form id="delete-doc-{{ $field }}" action="{{ route('admin.job-packages.delete-document', [$jobPackage, $field]) }}"
                method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endforeach

    <!-- Script JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi pembantu untuk mengubah input berformat teks/titik/koma menjadi angka bertipe float
        function parseFormattedNumber(val) {
            if (!val) return 0;
            let str = val.toString().trim();
            // Jika terdapat titik pemisah ribuan (contoh: 687.350.000 atau 10.000,00)
            if (str.includes('.') && str.includes(',')) {
                str = str.replace(/\./g, '').replace(',', '.');
            } else if (str.includes('.')) {
                const parts = str.split('.');
                if (parts.length > 2 || (parts.length === 2 && parts[1].length === 3)) {
                    str = str.replace(/\./g, '');
                }
            } else if (str.includes(',')) {
                str = str.replace(',', '.');
            }
            return parseFloat(str) || 0;
        }

        // 1. Kalkulasi Total Progres
        const rabInput = document.querySelector('input[name="rab_lp002"]');
        const pbjInput = document.querySelector('input[name="pbj_lp002"]');
        const progressInput = document.querySelector('input[name="progress_pekerjaan"]');
        const admInput = document.querySelector('input[name="proses_adm_keuangan"]');
        const totalDisplay = document.getElementById('total-progress-display');

        function calculateTotal() {
            const rab = parseFormattedNumber(rabInput?.value);
            const pbj = parseFormattedNumber(pbjInput?.value);
            const progress = parseFormattedNumber(progressInput?.value);
            const adm = parseFormattedNumber(admInput?.value);

            const total = (rab * 0.05) + (pbj * 0.05) + (progress * 0.85) + (adm * 0.05);
            let formattedTotal = Number.isInteger(total) ? total : total.toFixed(2);
            
            if (totalDisplay) {
                totalDisplay.textContent = formattedTotal + '%';

                if (total > 100) {
                    totalDisplay.classList.remove('text-blue-700');
                    totalDisplay.classList.add('text-red-600');
                } else {
                    totalDisplay.classList.remove('text-red-600');
                    totalDisplay.classList.add('text-blue-700');
                }
            }
        }

        [rabInput, pbjInput, progressInput, admInput].forEach(input => {
            if (input) input.addEventListener('input', calculateTotal);
        });
        calculateTotal();

        // 2. Dynamic PO Items Repeater & Auto Final Harga
        const poContainer = document.getElementById('po-container');
        const addPoBtn = document.getElementById('add-po-row');
        const finalHargaInput = document.getElementById('final_harga');

        function calculateFinalHarga() {
            let total = 0;
            document.querySelectorAll('.po-price-input').forEach(input => {
                total += parseFormattedNumber(input.value);
            });
            if (finalHargaInput) finalHargaInput.value = total;
        }

        function reindexPoRows() {
            const rows = poContainer.querySelectorAll('.po-row');
            rows.forEach((row, index) => {
                const noPoInput = row.querySelector('input[name*="[no_po]"]');
                const descInput = row.querySelector('input[name*="[description]"]');
                const priceInput = row.querySelector('input[name*="[price]"]');
                
                if (noPoInput) noPoInput.name = `pos[${index}][no_po]`;
                if (descInput) descInput.name = `pos[${index}][description]`;
                if (priceInput) priceInput.name = `pos[${index}][price]`;
            });
        }

        if (poContainer && addPoBtn) {
            poContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('po-price-input')) calculateFinalHarga();
            });

            poContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-po-row');
                if (removeBtn) {
                    const rows = poContainer.querySelectorAll('.po-row');
                    if (rows.length > 1) {
                        removeBtn.closest('.po-row').remove();
                        reindexPoRows();
                        calculateFinalHarga();
                    } else {
                        alert('Minimal harus ada 1 item Purchase Order.');
                    }
                }
            });

            addPoBtn.addEventListener('click', function() {
                const index = poContainer.querySelectorAll('.po-row').length;
                const newRowHTML = `
                    <div class="po-row grid grid-cols-1 sm:grid-cols-12 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 items-end">
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">No. PO</label>
                            <input type="text" name="pos[${index}][no_po]" placeholder="Contoh: PO-2026-001"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Item PO</label>
                            <input type="text" name="pos[${index}][description]" placeholder="Rincian / nama barang/jasa"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Harga (Rp)</label>
                            <input type="text" name="pos[${index}][price]" placeholder="0"
                                class="po-price-input w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c]">
                        </div>
                        <div class="sm:col-span-1 flex justify-end">
                            <button type="button" class="remove-po-row p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                poContainer.insertAdjacentHTML('beforeend', newRowHTML);
            });
        }

        // 3. Dynamic Surat/BAK/Doc Repeater (No. Dokumen)
        const suratContainer = document.getElementById('surat-container');
        const addSuratBtn = document.getElementById('add-surat-row');

        function reindexSuratRows() {
            const rows = suratContainer.querySelectorAll('.surat-row');
            rows.forEach((row, index) => {
                row.querySelector('textarea').name = `surat_bak_docs[${index}]`;
            });
        }

        if (suratContainer && addSuratBtn) {
            suratContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-surat-row');
                if (removeBtn) {
                    const rows = suratContainer.querySelectorAll('.surat-row');
                    if (rows.length > 1) {
                        removeBtn.closest('.surat-row').remove();
                        reindexSuratRows();
                    } else {
                        alert('Minimal harus ada 1 No. Dokumen.');
                    }
                }
            });

            addSuratBtn.addEventListener('click', function() {
                const index = suratContainer.querySelectorAll('.surat-row').length;
                const newRowHTML = `
                    <div class="surat-row flex gap-2 items-start">
                        <span class="shrink-0 w-7 h-9 flex items-center justify-center text-[11px] font-bold text-slate-400">-</span>
                        <textarea name="surat_bak_docs[${index}]" rows="2" required
                            placeholder="Contoh: 05570/E/TK/3310/IT/2026 019.BAK/INT.JPP/PIM/III/2026"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all resize-none"></textarea>
                        <button type="button" class="remove-surat-row shrink-0 p-2 mt-0.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                suratContainer.insertAdjacentHTML('beforeend', newRowHTML);
            });
        }

        // 4. Dynamic Permintaan Dari Repeater
        const permintaanContainer = document.getElementById('permintaan-container');
        const addPermintaanBtn = document.getElementById('add-permintaan-row');

        function reindexPermintaanRows() {
            const rows = permintaanContainer.querySelectorAll('.permintaan-row');
            rows.forEach((row, index) => {
                row.querySelector('input').name = `permintaan_dari[${index}]`;
            });
        }

        if (permintaanContainer && addPermintaanBtn) {
            permintaanContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-permintaan-row');
                if (removeBtn) {
                    const rows = permintaanContainer.querySelectorAll('.permintaan-row');
                    if (rows.length > 1) {
                        removeBtn.closest('.permintaan-row').remove();
                        reindexPermintaanRows();
                    } else {
                        removeBtn.closest('.permintaan-row').querySelector('input').value = '';
                    }
                }
            });

            addPermintaanBtn.addEventListener('click', function() {
                const index = permintaanContainer.querySelectorAll('.permintaan-row').length;
                const newRowHTML = `
                    <div class="permintaan-row flex gap-2 items-start">
                        <span class="shrink-0 w-7 h-9 flex items-center justify-center text-[11px] font-bold text-slate-400">-</span>
                        <input type="text" name="permintaan_dari[${index}]"
                            placeholder="Contoh: Dept. Rendal HAR"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all">
                        <button type="button" class="remove-permintaan-row shrink-0 p-2 mt-0.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                permintaanContainer.insertAdjacentHTML('beforeend', newRowHTML);
            });
        }

        // 5. Dynamic Judul Repeater (Tunggal per Baris)
        const judulContainer = document.getElementById('judul-container');
        const addJudulBtn = document.getElementById('add-judul-row');
        
        if (judulContainer && addJudulBtn) {
            judulContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-judul-row');
                if (removeBtn) {
                    if (judulContainer.querySelectorAll('.judul-row').length > 1) {
                        removeBtn.closest('.judul-row').remove();
                    } else {
                        alert('Minimal harus ada 1 Judul / Nama Barang.');
                    }
                }
            });

            addJudulBtn.addEventListener('click', function() {
                const newRowHTML = `
                    <div class="judul-row flex gap-2 items-center">
                        <span class="row-number shrink-0 w-7 h-9 flex items-center justify-center text-[11px] font-bold text-slate-400">-</span>
                        <input type="text" name="job_package[]" required placeholder="Contoh: Carry Roller BW750 (100ea)"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#0f2b5c] focus:bg-white transition-all">
                        <button type="button" class="remove-judul-row shrink-0 p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                judulContainer.insertAdjacentHTML('beforeend', newRowHTML);
            });
        }
    });
    </script>
</x-admin-layout>