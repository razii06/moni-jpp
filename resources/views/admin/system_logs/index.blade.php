<x-admin-layout>
    <x-slot name="title">Global System Logs</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">System Logs (Jejak Audit)</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Rekaman seluruh aktivitas yang terjadi di dalam sistem secara real-time.</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-widest text-slate-500 font-bold">
                        <th class="px-6 py-4">Waktu Kejadian</th>
                        <th class="px-6 py-4">Pelaku (User)</th>
                        <th class="px-6 py-4">Aksi</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs font-bold text-slate-600">
                                {{ $log->created_at->format('d M Y') }}
                            </span>
                            <br>
                            <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                                {{ $log->created_at->format('H:i:s') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0f2b5c] flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $log->user ? $log->user->name : 'Sistem / Guest' }}</p>
                                    <p class="text-xs text-slate-400">{{ $log->user ? $log->user->email : '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = 'bg-slate-100 text-slate-600 border-slate-200';
                                if(strtolower($log->action) == 'login') $color = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                if(strtolower($log->action) == 'logout') $color = 'bg-amber-50 text-amber-600 border-amber-200';
                            @endphp
                            <span class="px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wider rounded-md border {{ $color }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-slate-700 line-clamp-2">
                                {{ $log->description ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs font-mono text-slate-500 bg-slate-50 px-2 py-1 rounded-md border border-slate-100">
                                {{ $log->ip_address ?? 'Unknown' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-slate-500 font-bold text-sm">Belum ada aktivitas terekam.</h3>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
