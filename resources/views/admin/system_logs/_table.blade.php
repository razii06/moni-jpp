@php
    $docLabels = [
        'doc_rab'                => 'RAB',
        'doc_bak'                => 'BAK',
        'doc_surat_permintaan'   => 'Surat Permintaan',
        'doc_surat_izin_prinsip' => 'Surat Izin Prinsip',
        'doc_tor'                => 'TOR / KAK',
        'doc_bast'               => 'BAST',
    ];

    $formatDetails = function($text) use ($docLabels) {
        if (empty($text)) return 'Aktivitas sistem tercatat.';

        // Clean Multiple uploads (doc_rab, doc_bak -> RAB, BAK)
        if (preg_match('/Dokumen diunggah secara Multiple:\s*([^.]+)/i', $text, $matches)) {
            $rawDocs = explode(',', $matches[1]);
            $cleanDocs = [];
            foreach ($rawDocs as $d) {
                $d = trim($d);
                if (!empty($d)) {
                    $cleanDocs[] = $docLabels[$d] ?? ucwords(str_replace(['doc_', '_'], ['', ' '], $d));
                }
            }
            return 'Dokumen berhasil diunggah: ' . implode(', ', $cleanDocs) . '.';
        }

        // Clean doc deleted
        if (preg_match('/Seluruh Dokumen pada [“"]([^”"]+)[”"] dihapus/i', $text, $matches)) {
            $docKey = trim($matches[1]);
            $docName = $docLabels[$docKey] ?? $docKey;
            return 'Seluruh berkas dokumen ' . $docName . ' telah dihapus.';
        }

        // Clean standard job package descriptions
        if (str_contains($text, 'Job Package baru ditambahkan')) {
            return 'Job Package baru berhasil didaftarkan ke sistem.';
        }
        if (str_contains($text, 'Job Package dibatalkan')) {
            return 'Job Package telah dibatalkan oleh pengguna.';
        }
        if (str_contains($text, 'Job Package diaktifkan kembali')) {
            return 'Job Package telah diaktifkan kembali.';
        }
        if (str_contains($text, 'User berhasil masuk')) {
            return 'User berhasil masuk ke dalam sistem.';
        }
        if (str_contains($text, 'User keluar')) {
            return 'User telah keluar dari sistem.';
        }

        return $text;
    };
@endphp

<div class="bg-white border border-slate-200/90 rounded-lg shadow-2xs overflow-hidden">
    @if($logs->isEmpty())
        <div class="py-16 px-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 border border-slate-200 text-slate-400 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-800">Tidak ada audit log ditemukan</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                Tidak ada data log yang cocok dengan filter yang Anda tentukan. Silakan reset filter untuk melihat seluruh rekaman.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <!-- Table Header (Persis Referensi Acme) -->
                <thead>
                    <tr class="bg-[#edf2f7] border-b border-slate-200/90 text-xs font-semibold text-slate-600">
                        <th class="py-3 px-5 w-52 font-semibold">Time</th>
                        <th class="py-3 px-5 min-w-[240px] font-semibold">What changed?</th>
                        <th class="py-3 px-5 w-28 font-semibold">Action</th>
                        <th class="py-3 px-5 w-44 font-semibold">Performed by</th>
                        <th class="py-3 px-5 min-w-[280px] font-semibold">Details</th>
                    </tr>
                </thead>

                <!-- Table Body (Persis Referensi Acme) -->
                <tbody class="divide-y divide-slate-200/80 text-sm">
                    @foreach($logs as $log)
                        @php
                            $actionRaw = $log->action;
                            $actionLower = strtolower($actionRaw);

                            // Category Label & Target Title
                            $categoryLabel = 'System';
                            $targetTitle = 'Sistem Monitoring';
                            $actionPill = 'Updated';
                            $actionBadgeClass = 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]'; // Amber

                            // Extract project name if available
                            $projectName = null;
                            $rawDesc = $log->description ?? '';
                            $cleanDesc = $rawDesc;

                            if (preg_match("/^Pada Project '(.*?)':\s*(.*)$/s", $rawDesc, $matches)) {
                                $projectName = $matches[1];
                                $cleanDesc = $matches[2];
                            }

                            // Classify category, target title, and action badge (Acme Reference Style)
                            if (str_contains($actionLower, 'login')) {
                                $categoryLabel = 'Account';
                                $targetTitle = 'Sesi Pengguna';
                                $actionPill = 'Logged in';
                                $actionBadgeClass = 'bg-[#f0fdfa] text-[#0f766e] border border-[#99f6e4]'; // Teal
                            } elseif (str_contains($actionLower, 'logout')) {
                                $categoryLabel = 'Account';
                                $targetTitle = 'Sesi Pengguna';
                                $actionPill = 'Logged out';
                                $actionBadgeClass = 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]'; // Amber
                            } elseif (str_contains($actionLower, 'document_uploaded') || str_contains($actionLower, 'upload')) {
                                $categoryLabel = 'Dokumen Berkas';
                                $targetTitle = $projectName ?? 'Berkas Job Package';
                                $actionPill = 'Uploaded';
                                $actionBadgeClass = 'bg-[#eff6ff] text-[#1d4ed8] border border-[#bfdbfe]'; // Blue
                            } elseif (str_contains($actionLower, 'document_deleted')) {
                                $categoryLabel = 'Dokumen Berkas';
                                $targetTitle = $projectName ?? 'Berkas Job Package';
                                $actionPill = 'Deleted';
                                $actionBadgeClass = 'bg-[#fff1f2] text-[#be123c] border border-[#fecdd3]'; // Rose
                            } elseif (str_contains($actionLower, 'created') || str_contains($actionLower, 'tambah')) {
                                if (str_contains($actionLower, 'user')) {
                                    $categoryLabel = 'Agent';
                                    $targetTitle = $log->user ? $log->user->name : 'Data User';
                                } else {
                                    $categoryLabel = 'Job Package';
                                    $targetTitle = $projectName ?? 'Job Package Baru';
                                }
                                $actionPill = 'Created';
                                $actionBadgeClass = 'bg-[#ecfdf5] text-[#047857] border border-[#a7f3d0]'; // Emerald
                            } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy') || str_contains($actionLower, 'cancel') || str_contains($actionLower, 'batal') || str_contains($actionLower, 'hapus')) {
                                $categoryLabel = 'Job Package';
                                $targetTitle = $projectName ?? 'Job Package';
                                $actionPill = 'Cancelled';
                                $actionBadgeClass = 'bg-[#fff1f2] text-[#be123c] border border-[#fecdd3]'; // Rose
                            } elseif (str_contains($actionLower, 'reactivate') || str_contains($actionLower, 'aktif')) {
                                $categoryLabel = 'Job Package';
                                $targetTitle = $projectName ?? 'Job Package';
                                $actionPill = 'Updated';
                                $actionBadgeClass = 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]'; // Amber
                            } elseif (str_contains($actionLower, '2fa') || str_contains($actionLower, 'security')) {
                                $categoryLabel = 'Security';
                                $targetTitle = 'Autentikasi 2FA';
                                $actionPill = 'Updated';
                                $actionBadgeClass = 'bg-[#f5f3ff] text-[#6d28d9] border border-[#ddd6fe]'; // Purple
                            } elseif (str_contains($actionLower, 'password')) {
                                $categoryLabel = 'Security';
                                $targetTitle = 'Kata Sandi Akun';
                                $actionPill = 'Updated';
                                $actionBadgeClass = 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]'; // Amber
                            } elseif (str_contains($actionLower, 'profile')) {
                                $categoryLabel = 'Account';
                                $targetTitle = 'Profil Pengguna';
                                $actionPill = 'Updated';
                                $actionBadgeClass = 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]'; // Amber
                            } else {
                                $categoryLabel = str_contains($actionLower, 'job') ? 'Job Package' : 'System';
                                $targetTitle = $projectName ?? 'Sistem Data';
                                $actionPill = 'Updated';
                                $actionBadgeClass = 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]'; // Amber
                            }

                            $humanDetails = $formatDetails($cleanDesc);

                            // Modal detail payload
                            $modalData = [
                                'id' => str_pad($log->id, 5, '0', STR_PAD_LEFT),
                                'time_full' => $log->created_at->format('D, d M, Y \a\t H:i:s') . ' WIB',
                                'time_relative' => $log->created_at->locale('id')->diffForHumans(),
                                'user_name' => $log->user ? $log->user->name : 'System',
                                'user_email' => $log->user ? $log->user->email : '-',
                                'user_role' => $log->user ? ($log->user->role ?? 'User') : 'System',
                                'category' => $categoryLabel,
                                'action_pill' => $actionPill,
                                'action_raw' => $log->action,
                                'project' => $projectName,
                                'target_title' => $targetTitle,
                                'description' => $humanDetails,
                                'raw_description' => $log->description,
                                'ip_address' => $log->ip_address ?? '127.0.0.1',
                            ];
                        @endphp

                        <!-- Table Row (Clean, Consistent Height, Smooth Hover) -->
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- 1. Time Column (Format Single Line / 2 Clean Lines seperti Referensi) -->
                            <td class="py-3.5 px-5 align-top whitespace-nowrap">
                                <p class="text-sm font-normal text-slate-700">
                                    {{ $log->created_at->format('D, d M, Y') }} at {{ $log->created_at->format('H:i') }} WIB
                                </p>
                            </td>

                            <!-- 2. What changed? Column (Category on top, Target title below) -->
                            <td class="py-3.5 px-5 align-top">
                                <p class="text-xs font-medium text-slate-400 mb-0.5">
                                    {{ $categoryLabel }}
                                </p>
                                <p class="text-sm font-semibold text-slate-800 leading-snug">
                                    {{ $targetTitle }}
                                </p>
                            </td>

                            <!-- 3. Action Column (Subtle Rounded Badge) -->
                            <td class="py-3.5 px-5 align-top whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded {{ $actionBadgeClass }}">
                                    {{ $actionPill }}
                                </span>
                            </td>

                            <!-- 4. Performed by Column (Name on top, IP underneath) -->
                            <td class="py-3.5 px-5 align-top whitespace-nowrap">
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $log->user ? $log->user->name : 'System' }}
                                </p>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </p>
                            </td>

                            <!-- 5. Details Column (Summary sentence + View more > link) -->
                            <td class="py-3.5 px-5 align-top">
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    {{ $humanDetails }}
                                </p>
                                <button type="button" 
                                        @click="openDetailModal({{ json_encode($modalData) }})"
                                        class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline mt-1 inline-flex items-center gap-0.5 cursor-pointer">
                                    <span>View more</span>
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Pagination Footer -->
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-200/90 bg-[#f8fafc]">
            {{ $logs->links() }}
        </div>
    @endif
</div>
