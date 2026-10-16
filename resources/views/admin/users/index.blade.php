<x-admin-layout>
    <x-slot name="title">Kelola Pengguna</x-slot>

    @if(!auth()->user()->isAdmin())
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm font-semibold text-amber-800">
        Halaman Manajemen Pengguna hanya dapat dibuka oleh akun dengan role <strong>admin</strong>.
        Akun Anda saat ini memiliki role <strong>{{ auth()->user()->role }}</strong>.
    </div>
    @else

    <div class="space-y-6">
        <!-- Alert Notifikasi Error -->
        @if (session('error'))
        <div
            class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-700 text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <!-- Alert Notifikasi Sukses -->
        @if (session('success'))
        <div
            class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-700 text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Buat Akun Baru (Kiri - 1 Kolom) -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm h-fit">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah Akun Baru
                </h3>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:border-amber-500 focus:bg-white focus:outline-none transition-all"
                            placeholder="Budi Santoso">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Username (Untuk
                            Login)</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:border-amber-500 focus:bg-white focus:outline-none transition-all"
                            placeholder="budi_jpp">
                        @error('username') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:border-amber-500 focus:bg-white focus:outline-none transition-all"
                            placeholder="budi@pim.co.id">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kata Sandi</label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:border-amber-500 focus:bg-white focus:outline-none transition-all"
                            placeholder="••••••••">
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Pada bagian Form Input Role -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Role / Hak Akses</label>
                        <select name="role" required
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:border-amber-500 focus:bg-white focus:outline-none transition-all">
                            <option value="super_vc">Supervisor</option>
                            <option value="pic_jpp">PIC JPP</option>
                            <option value="admin">Admin System</option>
                        </select>
                        @error('role') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                        Buatkan Akun
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Akun Terdaftar (Kanan - 2 Kolom) -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Daftar Akun Terdaftar</h3>
                        <p class="text-xs text-slate-500">Kelola kredensial, hak akses, dan status keamanan akun pengguna.</p>
                    </div>

                    <!-- Mini Widget Ringkasan Kepatuhan 2FA -->
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200/90 rounded-xl self-start sm:self-auto shadow-2xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $twoFactorPercent >= 80 ? 'bg-emerald-400' : ($twoFactorPercent >= 50 ? 'bg-amber-400' : 'bg-rose-400') }} opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 {{ $twoFactorPercent >= 80 ? 'bg-emerald-500' : ($twoFactorPercent >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                        </span>
                        <div class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold">
                            <span>Proteksi 2FA:</span>
                            <span class="font-bold text-slate-900">{{ $twoFactorCount }}/{{ $totalUsers }}</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $twoFactorPercent >= 80 ? 'bg-emerald-100 text-emerald-800' : ($twoFactorPercent >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ $twoFactorPercent }}%
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4">Nama</th>
                                <th class="py-3.5 px-4">Username</th>
                                <th class="py-3.5 px-4">Email</th>
                                <th class="py-3.5 px-4">Role</th>
                                <th class="py-3.5 px-4 text-center">Status 2FA</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-800">{{ $user->name }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    @if ($user->username)
                                        <span class="text-slate-400 font-normal">@</span>{{ $user->username }}
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">{{ $user->email }}</td>

                                <!-- Pada bagian Tabel Daftar Akun -->
                                <td class="py-3.5 px-4">
                                    @php
                                    $roleStr = strtolower($user->role ?? '');
                                    $badgeClass = 'bg-slate-100 text-slate-800 border-slate-200'; // default
                                    $roleName = 'User';

                                    if ($roleStr === 'admin') {
                                    $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                                    $roleName = 'ADMIN';
                                    } elseif ($roleStr === 'pic_jpp' || $roleStr === 'staff') {
                                    $badgeClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                    $roleName = 'PIC JPP';
                                    } elseif ($roleStr === 'super_vc') {
                                    $badgeClass = 'bg-purple-100 text-purple-800 border-purple-200';
                                    $roleName = 'SUPERVISOR';
                                    }
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full border {{ $badgeClass }}">
                                        {{ $roleName }}
                                    </span>
                                </td>

                                <!-- KOLOM STATUS 2FA -->
                                <td class="py-3.5 px-4 text-center">
                                    @if ($user->hasTwoFactor())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full shadow-2xs"
                                        title="2FA Aktif - Akun terlindungi OTP Google Authenticator">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded-full"
                                        title="2FA Belum Aktif - Akun hanya dilindungi kata sandi">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Nonaktif
                                    </span>
                                    @endif
                                </td>

                                <!-- KOLOM AKSI -->
                                <td class="py-3.5 px-4 text-center">
                                    @if (auth()->id() !== $user->id)
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Tombol Reset 2FA oleh Admin (Hanya jika user mengaktifkan 2FA) --}}
                                        @if ($user->hasTwoFactor())
                                        <form action="{{ route('admin.users.reset-2fa', $user->id) }}" method="POST"
                                            class="inline"
                                            data-confirm-delete="Kunci otentikasi 2FA pengguna ini akan dihapus. Pengguna dapat login kembali tanpa verifikasi OTP dan dapat melakukan setup ulang jika diinginkan."
                                            data-confirm-title="Reset 2FA Pengguna?"
                                            data-item-name="{{ $user->name }} ({{ $user->username }})"
                                            data-confirm-btn="Ya, Reset 2FA"
                                            data-confirm-type="warning">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 text-amber-600 hover:bg-amber-50 hover:text-amber-700 rounded-lg transition-colors cursor-pointer"
                                                title="Reset 2FA Pengguna (Gunakan jika HP/perangkat user hilang atau bermasalah)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Tombol Hapus User --}}
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            class="inline"
                                            data-confirm-delete="Pengguna ini tidak akan dapat login lagi ke sistem Monitoring JPP."
                                            data-confirm-title="Hapus Akun Pengguna?"
                                            data-item-name="{{ $user->name }} ({{ $user->username }})">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-lg transition-colors cursor-pointer"
                                                title="Hapus User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-[11px] text-slate-400 font-medium italic">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <!-- colspan diubah menjadi 6 untuk mengakomodasi kolom status 2fa -->
                                <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
                                    Belum ada data pengguna.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-admin-layout>