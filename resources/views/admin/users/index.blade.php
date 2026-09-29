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
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-700 text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                            <option value="super_vc">Supervisi</option>
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
                <h3 class="text-base font-bold text-slate-800 mb-4">Daftar Akun Terdaftar</h3>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4">Nama</th>
                                <th class="py-3.5 px-4">Username</th>
                                <th class="py-3.5 px-4">Email</th>
                                <th class="py-3.5 px-4">Role</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-800">{{ $user->name }}</td>
                                <td class="py-3.5 px-4 text-amber-600 font-mono font-semibold">
                                    {{ $user->username ?? '-' }}</td>
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
                                            $roleName = 'SUPERVISI';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full border {{ $badgeClass }}">
                                        {{ $roleName }}
                                    </span>
                                </td>

                                <!-- KOLOM AKSI HAPUS -->
                                <td class="py-3.5 px-4 text-center">
                                    @if (auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-lg transition-colors" title="Hapus User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <!-- colspan diubah menjadi 5 untuk mengakomodasi kolom aksi -->
                                <td colspan="5" class="py-8 text-center text-slate-400 font-medium">
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