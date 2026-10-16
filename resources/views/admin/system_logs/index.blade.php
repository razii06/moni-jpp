<x-admin-layout>
    <x-slot name="title">Audit Log — Admin</x-slot>

    <div x-data="systemLogWatcher()" @keydown.escape.window="detailModalOpen = false; openFilterDrawer = false" class="space-y-4">
        <!-- Top Bar Header & Breadcrumbs (Persis Referensi Acme) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pt-1 pb-1">
            <div>
                <!-- Breadcrumbs (Persis Acme: Admin > Audit Log) -->
                <div class="flex items-center gap-1.5 text-sm font-semibold">
                    <span class="text-blue-600 hover:text-blue-700 cursor-pointer">Admin</span>
                    <span class="text-slate-400 font-normal">&gt;</span>
                    <span class="text-slate-900 font-bold">Audit Log</span>
                </div>
            </div>

            <!-- Top Right Action Controls -->
            <div class="flex flex-wrap items-center gap-2.5 self-stretch sm:self-auto justify-end">
                <!-- Status Terakhir Diperbarui -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200/90 rounded-md text-xs text-slate-500 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    <span class="text-[11px] text-slate-400">Update:</span>
                    <span class="font-mono font-medium text-slate-700" x-text="lastUpdatedTime">{{ now()->format('H:i:s') }}</span>
                </div>

                <!-- Tombol Toggle Auto-Refresh / Live Mode -->
                <button type="button"
                        @click="toggleAutoRefresh()"
                        :class="autoRefresh 
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-1 ring-emerald-400/30' 
                            : 'bg-white hover:bg-slate-50 text-slate-600 border-slate-200/90'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border text-xs font-medium transition-colors shadow-2xs cursor-pointer select-none">
                    <template x-if="autoRefresh">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </template>
                    <template x-if="!autoRefresh">
                        <span class="inline-block w-2 h-2 rounded-full bg-slate-300"></span>
                    </template>
                    <span x-show="!autoRefresh">Auto-Refresh</span>
                    <span x-show="autoRefresh" class="font-semibold" style="display: none;">
                        LIVE (<span x-text="countdown + 's'">30s</span>)
                    </span>
                </button>

                <!-- Tombol Trigger Slide-Over Filter Panel (Persis Desain 2) -->
                <button type="button"
                        @click="openFilterDrawer = true"
                        :class="activeFilterCount > 0 
                            ? 'bg-blue-50 text-blue-700 border-blue-300 ring-2 ring-blue-500/20 font-semibold' 
                            : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/90 font-medium'"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-md border text-xs shadow-2xs transition-colors cursor-pointer select-none group">
                    <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filter</span>
                    
                    <template x-if="activeFilterCount > 0">
                        <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-600 text-white" x-text="activeFilterCount"></span>
                    </template>
                </button>
            </div>
        </div>

        <!-- Active Filter Indicator Ribbon (Jika Ada Filter Aktif) -->
        <template x-if="activeFilterCount > 0">
            <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <span class="font-semibold text-slate-600 flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter:
                </span>

                <!-- Search Chip -->
                <template x-if="filters.search">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white text-slate-700 border border-slate-200 shadow-2xs">
                        <span class="text-slate-400">Kata kunci:</span>
                        <strong x-text="filters.search"></strong>
                        <button type="button" @click="filters.search = ''; applyFilters()" class="text-slate-400 hover:text-rose-600 ml-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>

                <!-- Category Chip -->
                <template x-if="filters.category && filters.category !== 'semua'">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white text-slate-700 border border-slate-200 shadow-2xs">
                        <span class="text-slate-400">Tipe:</span>
                        <strong x-text="getCategoryLabel(filters.category)"></strong>
                        <button type="button" @click="filters.category = 'semua'; applyFilters()" class="text-slate-400 hover:text-rose-600 ml-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>

                <!-- User Chip -->
                <template x-if="filters.user_id && filters.user_id !== 'semua'">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white text-slate-700 border border-slate-200 shadow-2xs">
                        <span class="text-slate-400">User:</span>
                        <strong x-text="getUserName(filters.user_id)"></strong>
                        <button type="button" @click="filters.user_id = 'semua'; applyFilters()" class="text-slate-400 hover:text-rose-600 ml-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>

                <!-- Date Range Chip -->
                <template x-if="filters.date_range && filters.date_range !== 'semua'">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white text-slate-700 border border-slate-200 shadow-2xs">
                        <span class="text-slate-400">Waktu:</span>
                        <strong x-text="getDateRangeLabel(filters.date_range)"></strong>
                        <button type="button" @click="filters.date_range = 'semua'; filters.date_from = ''; filters.date_to = ''; applyFilters()" class="text-slate-400 hover:text-rose-600 ml-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>

                <button type="button" 
                        @click="resetFilters()" 
                        class="text-xs font-semibold text-blue-600 hover:text-blue-800 ml-auto cursor-pointer">
                    Clear all
                </button>
            </div>
        </template>

        <!-- Tabel Data Wrapper (Dapat Diperbarui Dinamis Tanpa Kedip) -->
        <div class="relative">
            <!-- Progress Line Indicator saat Loading -->
            <div x-show="isLoading" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute top-0 left-0 right-0 h-0.5 bg-blue-100 overflow-hidden rounded-t-xl z-10"
                 style="display: none;">
                <div class="h-full bg-blue-600 animate-pulse w-full"></div>
            </div>

            <div id="logs-container" 
                 x-ref="tableContainer" 
                 :class="isLoading ? 'opacity-60 pointer-events-none transition-opacity duration-200' : 'opacity-100 transition-opacity duration-200'">
                @include('admin.system_logs._table')
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- SLIDE-OVER (DRAWER) FILTER PANEL (PERSIS SEPERTI DESAIN 2)    -->
        <!-- ============================================================ -->
        <template x-teleport="body">
            <div x-show="openFilterDrawer" 
                 x-cloak
                 class="relative z-[9999]">
                
                <!-- Backdrop Overlay -->
                <div x-show="openFilterDrawer"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="openFilterDrawer = false"
                     class="fixed inset-0 bg-slate-900/30 backdrop-blur-2xs transition-opacity"></div>

                <!-- Slide-Over Drawer Container (Modern Floating Flyout Card) -->
                <div class="fixed inset-0 overflow-y-auto pointer-events-none p-4 sm:p-6 md:p-8 flex justify-end items-start pt-14 sm:pt-16 md:pt-20">
                    
                    <!-- Panel Content: Modern Floating Card -->
                    <div x-show="openFilterDrawer"
                         x-transition:enter="transform transition ease-out duration-250"
                         x-transition:enter-start="translate-x-6 opacity-0 scale-98"
                         x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                         x-transition:leave="transform transition ease-in duration-200"
                         x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                         x-transition:leave-end="translate-x-6 opacity-0 scale-98"
                         class="pointer-events-auto w-full max-w-sm bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-slate-200/90 flex flex-col overflow-hidden max-h-[calc(100vh-6rem)]">
                        
                        <!-- Drawer Header (Floating Card Header) -->
                        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/80">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">Filter Log</h3>
                                <template x-if="activeFilterCount > 0">
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-600 text-white" x-text="activeFilterCount"></span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="resetFilters()" 
                                        class="text-xs font-semibold text-slate-400 hover:text-blue-600 transition-colors px-2 py-1 rounded cursor-pointer">
                                    Clear all
                                </button>
                                <button type="button" 
                                        @click="openFilterDrawer = false" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition-colors cursor-pointer"
                                        title="Tutup (Esc)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Drawer Form Fields -->
                        <div class="overflow-y-auto px-5 py-4 space-y-4 text-xs">
                            <!-- 1. Date Range Filter -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">
                                    Rentang Waktu (Date Range)
                                </label>
                                <select x-model="filters.date_range" class="w-full text-xs font-normal rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500/20 py-2 text-slate-700 bg-white">
                                    <option value="semua">Semua Waktu (All Dates)</option>
                                    <option value="today">Hari Ini</option>
                                    <option value="yesterday">Kemarin</option>
                                    <option value="7_days">7 Hari Terakhir</option>
                                    <option value="30_days">30 Hari Terakhir</option>
                                    <option value="this_month">Bulan Ini</option>
                                    <option value="custom">Pilih Rentang Tanggal Khusus...</option>
                                </select>

                                <!-- Custom Date Pickers -->
                                <div x-show="filters.date_range === 'custom'" class="pt-2 grid grid-cols-2 gap-2" style="display: none;">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block mb-1">Dari Tanggal:</span>
                                        <input type="date" x-model="filters.date_from" class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-500 py-1.5 text-slate-700">
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block mb-1">Sampai:</span>
                                        <input type="date" x-model="filters.date_to" class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-500 py-1.5 text-slate-700">
                                    </div>
                                </div>
                            </div>

                            <!-- 2. What changed? Filter -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">
                                    Kategori Perubahan (What changed?)
                                </label>
                                <select x-model="filters.category" class="w-full text-xs font-normal rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500/20 py-2 text-slate-700 bg-white">
                                    <option value="semua">Semua Tipe Kategori</option>
                                    <option value="job_package">Job Package</option>
                                    <option value="document">Dokumen Berkas</option>
                                    <option value="auth">Account (Sesi Pengguna)</option>
                                    <option value="security">Security (2FA & Keamanan)</option>
                                    <option value="user_management">User Management</option>
                                    <option value="documentation">Dokumentasi Beranda</option>
                                </select>
                            </div>

                            <!-- 3. Performed by Filter -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">
                                    Pelaku Aksi (Performed by)
                                </label>
                                <select x-model="filters.user_id" class="w-full text-xs font-normal rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500/20 py-2 text-slate-700 bg-white">
                                    <option value="semua">Semua Pengguna</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 4. Search Keyword -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">
                                    Pencarian Kata Kunci (Search)
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           x-model="filters.search" 
                                           placeholder="Cari deskripsi, aksi, atau IP..."
                                           class="w-full text-xs font-normal rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500/20 pl-8 py-2 text-slate-700">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Drawer Footer -->
                        <div class="p-3.5 px-5 border-t border-slate-100 flex items-center justify-end gap-2.5 bg-slate-50/80 shrink-0">
                            <button type="button" 
                                    @click="openFilterDrawer = false" 
                                    class="px-4 py-2 rounded-xl border border-slate-300/80 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    @click="applyFilters()" 
                                    class="px-5 py-2 rounded-xl bg-[#0f2b5c] hover:bg-[#0a1d40] text-white text-xs font-bold shadow-sm transition-all active:scale-95 cursor-pointer">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ============================================================ -->
        <!-- DETAIL MODAL (VIEW MORE TRIGGER DARI DESAIN 2)               -->
        <!-- ============================================================ -->
        <template x-teleport="body">
            <div x-show="detailModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-[9999] overflow-y-auto">
                
                <div class="flex items-center justify-center min-h-screen p-4 text-center">
                    <!-- Modal Backdrop -->
                    <div x-show="detailModalOpen"
                         x-transition:enter="ease-out duration-250"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="detailModalOpen = false"
                         class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>

                    <!-- Modal Dialog (Selalu Tepat di Tengah Layar Viewport Pengguna) -->
                    <div x-show="detailModalOpen"
                         x-transition:enter="ease-out duration-250"
                         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                         class="relative bg-white rounded-2xl max-w-lg w-full p-6 text-left shadow-2xl border border-slate-200 overflow-hidden z-10 space-y-4 my-8">
                        
                        <!-- Header -->
                        <div class="flex items-start justify-between pb-3.5 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-medium text-slate-400" x-text="'Ref: #' + (selectedLog?.id ?? '')"></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600" x-text="selectedLog?.category"></span>
                                </div>
                                <h3 class="text-base font-bold text-slate-800 mt-1" x-text="selectedLog?.action_pill ?? 'Detail Log'"></h3>
                            </div>
                            <button type="button" @click="detailModalOpen = false" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="space-y-3 text-xs">
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-0.5">
                                <span class="text-[10px] uppercase font-semibold text-slate-400">Time</span>
                                <p class="font-medium text-slate-800" x-text="selectedLog?.time_full"></p>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-0.5">
                                <span class="text-[10px] uppercase font-semibold text-slate-400">Performed by</span>
                                <p class="font-semibold text-slate-800" x-text="selectedLog?.user_name"></p>
                                <p class="text-slate-500 font-mono" x-text="'IP Address: ' + (selectedLog?.ip_address ?? '-')"></p>
                            </div>

                            <template x-if="selectedLog?.project">
                                <div class="bg-blue-50/40 p-3 rounded-lg border border-blue-100 space-y-0.5">
                                    <span class="text-[10px] uppercase font-semibold text-blue-600">What changed?</span>
                                    <p class="font-semibold text-slate-800 leading-snug" x-text="selectedLog?.project"></p>
                                </div>
                            </template>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-0.5">
                                <span class="text-[10px] uppercase font-semibold text-slate-400">Details</span>
                                <p class="font-normal text-slate-700 leading-relaxed whitespace-pre-line" x-text="selectedLog?.raw_description"></p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="button" @click="detailModalOpen = false" class="px-4 py-2 rounded-md bg-slate-900 text-white text-xs font-medium hover:bg-slate-800 transition-colors cursor-pointer">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Script Reaktivitas Alpine.js (State, AJAX Filter & Drawer Controller) -->
    <script>
        function systemLogWatcher() {
            const urlParams = new URLSearchParams(window.location.search);
            const userLookup = {
                @foreach($users as $u)
                    '{{ $u->id }}': '{{ addslashes($u->name) }}',
                @endforeach
            };

            return {
                isLoading: false,
                lastUpdatedTime: '{{ now()->format("H:i:s") }}',
                totalLogs: {{ $logs->total() }},
                justUpdated: false,
                autoRefresh: false,
                countdown: 30,
                countdownTimer: null,

                // State Drawer & Modal
                openFilterDrawer: false,
                detailModalOpen: false,
                selectedLog: null,

                init() {
                    this.$watch('detailModalOpen', val => {
                        document.body.classList.toggle('overflow-hidden', val || this.openFilterDrawer);
                    });
                    this.$watch('openFilterDrawer', val => {
                        document.body.classList.toggle('overflow-hidden', val || this.detailModalOpen);
                    });
                },

                // Filter Model (Diinisialisasi dari URL saat ini)
                filters: {
                    search: urlParams.get('search') || '',
                    category: urlParams.get('category') || 'semua',
                    user_id: urlParams.get('user_id') || 'semua',
                    date_range: urlParams.get('date_range') || 'semua',
                    date_from: urlParams.get('date_from') || '',
                    date_to: urlParams.get('date_to') || '',
                },

                get activeFilterCount() {
                    let count = 0;
                    if (this.filters.search && this.filters.search.trim() !== '') count++;
                    if (this.filters.category && this.filters.category !== 'semua') count++;
                    if (this.filters.user_id && this.filters.user_id !== 'semua') count++;
                    if (this.filters.date_range && this.filters.date_range !== 'semua') count++;
                    if (this.filters.date_from || this.filters.date_to) count++;
                    return count;
                },

                getCategoryLabel(cat) {
                    const map = {
                        'job_package': 'Job Package',
                        'document': 'Dokumen Berkas',
                        'auth': 'Account',
                        'security': 'Security',
                        'user_management': 'Agent',
                        'documentation': 'Change'
                    };
                    return map[cat] || cat;
                },

                getUserName(id) {
                    return userLookup[id] || ('User #' + id);
                },

                getDateRangeLabel(range) {
                    const map = {
                        'today': 'Hari Ini',
                        'yesterday': 'Kemarin',
                        '7_days': '7 Hari Terakhir',
                        '30_days': '30 Hari Terakhir',
                        'this_month': 'Bulan Ini',
                        'custom': 'Kustom'
                    };
                    return map[range] || range;
                },

                async applyFilters() {
                    this.openFilterDrawer = false;

                    const params = new URLSearchParams();
                    if (this.filters.search) params.set('search', this.filters.search.trim());
                    if (this.filters.category && this.filters.category !== 'semua') params.set('category', this.filters.category);
                    if (this.filters.user_id && this.filters.user_id !== 'semua') params.set('user_id', this.filters.user_id);
                    if (this.filters.date_range && this.filters.date_range !== 'semua') params.set('date_range', this.filters.date_range);
                    if (this.filters.date_from) params.set('date_from', this.filters.date_from);
                    if (this.filters.date_to) params.set('date_to', this.filters.date_to);

                    const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
                    window.history.pushState({}, '', newUrl);

                    await this.refreshLogs(true);
                },

                resetFilters() {
                    this.filters.search = '';
                    this.filters.category = 'semua';
                    this.filters.user_id = 'semua';
                    this.filters.date_range = 'semua';
                    this.filters.date_from = '';
                    this.filters.date_to = '';
                    this.applyFilters();
                },

                openDetailModal(logData) {
                    this.selectedLog = logData;
                    this.detailModalOpen = true;
                },

                async refreshLogs(manual = true) {
                    if (this.isLoading) return;
                    this.isLoading = true;

                    try {
                        const response = await fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Gagal memuat log');

                        const data = await response.json();
                        if (data && data.html) {
                            this.$refs.tableContainer.innerHTML = data.html;
                            this.lastUpdatedTime = data.timestamp;
                            this.totalLogs = data.total;
                            
                            this.justUpdated = true;
                            setTimeout(() => {
                                this.justUpdated = false;
                            }, 2500);
                        }
                    } catch (err) {
                        console.error('Terjadi kendala saat menyegarkan data log:', err);
                    } finally {
                        this.isLoading = false;
                        if (this.autoRefresh) {
                            this.countdown = 30;
                        }
                    }
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                    if (this.autoRefresh) {
                        this.startAutoRefresh();
                    } else {
                        this.stopAutoRefresh();
                    }
                },

                startAutoRefresh() {
                    this.countdown = 30;
                    if (this.countdownTimer) clearInterval(this.countdownTimer);
                    this.countdownTimer = setInterval(() => {
                        if (this.countdown > 1) {
                            this.countdown--;
                        } else {
                            this.countdown = 30;
                            this.refreshLogs(false);
                        }
                    }, 1000);
                },

                stopAutoRefresh() {
                    if (this.countdownTimer) {
                        clearInterval(this.countdownTimer);
                        this.countdownTimer = null;
                    }
                    this.countdown = 30;
                }
            }
        }
    </script>
</x-admin-layout>
