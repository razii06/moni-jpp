<x-admin-layout>
    <x-slot name="title">Kelola Pengguna</x-slot>

    @if(!auth()->user()->isAdmin())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm font-semibold text-amber-800">
            Halaman Manajemen Pengguna hanya dapat dibuka oleh akun dengan role <strong>admin</strong>.
            Akun Anda saat ini memiliki role <strong>{{ auth()->user()->role }}</strong>.
        </div>
    @else

    <div class="space-y-6">
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

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Role / Hak Akses</label>
                        <select name="role" required
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:border-amber-500 focus:bg-white focus:outline-none transition-all">
                            <option value="staff">Staff / PIC JPP</option>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-800">{{ $user->name }}</td>
                                <td class="py-3.5 px-4 text-amber-600 font-mono font-semibold">
                                    {{ $user->username ?? '-' }}</td>
                                <td class="py-3.5 px-4">{{ $user->email }}</td>
                                <td class="py-3.5 px-4">
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full {{ ($user->role ?? '') === 'admin' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                        {{ $user->role ?? 'User' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-medium">
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