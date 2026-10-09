<x-admin-layout title="Pengaturan 2FA">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Two-Factor Authentication (2FA)</h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Tingkatkan keamanan akun Anda menggunakan aplikasi Google Authenticator.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($isEnabled)
                <!-- Kondisi 2FA Aktif -->
                <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-emerald-800">Status: Aktif</h3>
                        <p class="text-sm text-emerald-600 mt-1">Akun Anda saat ini terlindungi oleh Two-Factor Authentication. Anda akan diminta memasukkan kode dari aplikasi Authenticator setiap kali login.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.2fa.disable') }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA? Keamanan akun akan menurun.')" class="whitespace-nowrap px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg transition text-sm shadow-md">
                            Nonaktifkan 2FA
                        </button>
                    </form>
                </div>
            @else
                <!-- Kondisi Setup 2FA -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center">1</span>
                            <p class="text-sm text-slate-700 pt-1.5">Unduh aplikasi <strong>Google Authenticator</strong> di HP Anda (Tersedia di Play Store / App Store).</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center">2</span>
                            <p class="text-sm text-slate-700 pt-1.5">Buka aplikasi tersebut lalu pilih ikon tambah (+) dan pilih <strong>"Scan a QR code"</strong>.</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center">3</span>
                            <p class="text-sm text-slate-700 pt-1.5">Pindai kode QR yang ada di sebelah kanan.</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center">4</span>
                            <div class="pt-1.5">
                                <p class="text-sm text-slate-700 mb-2">Masukkan 6 digit angka yang muncul di aplikasi untuk memverifikasi dan mengaktifkan.</p>
                                <form method="POST" action="{{ route('admin.2fa.enable') }}" class="flex gap-2">
                                    @csrf
                                    <div>
                                        <input type="text" name="otp" required maxlength="6" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 text-slate-800 rounded-lg text-center font-mono font-bold tracking-widest focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="123456">
                                        @if($errors->has('otp'))
                                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $errors->first('otp') }}</p>
                                        @endif
                                    </div>
                                    <button type="submit" class="px-5 py-2 bg-[#0f2b5c] hover:bg-blue-900 text-white font-bold rounded-lg transition shadow-md">Aktifkan</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Display -->
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 mb-4 inline-block">
                            {!! $qrCodeSvg !!}
                        </div>
                        <p class="text-xs text-slate-500 font-medium text-center">Jika tidak bisa memindai kode QR, gunakan kode manual ini:</p>
                        <code class="block mt-2 px-3 py-1.5 bg-slate-200 text-slate-700 font-mono font-bold text-sm rounded">{{ $secret }}</code>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
