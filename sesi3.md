# Ringkasan Sesi 3: Pengembangan UI dan Sistem Keamanan (Moni-JPP)

Sesi ini berfokus pada penyempurnaan antarmuka pengguna (UI) di halaman utama dan dashboard, serta peningkatan keamanan sistem secara menyeluruh melalui implementasi reCAPTCHA dan otentikasi dua faktor (2FA).

## 1. Penyempurnaan Antarmuka Pengguna (UI)
*   **Penyesuaian Desain Card:** Menyamakan desain, warna, dan gaya dari card "Penjelasan Sistem" dan "Dokumentasi Umum" agar senada dengan card "Informasi JPP". Hal ini dilakukan tanpa mengurangi keterbacaan teks di dalam card tersebut.
*   **Pembaruan Chart Dashboard:** Mengganti chart bawaan pada halaman dashboard admin dengan **ApexCharts** (tipe *Donut Chart*). Modifikasi dilakukan untuk memperbaiki visibilitas teks (persentase) agar tidak bertabrakan dengan background, serta memperbesar ukuran chart agar teks "Total Aktif" di tengahnya terlihat lebih jelas dan proporsional.

## 2. Peningkatan Keamanan Sistem (Opsi A & Opsi B)
Berdasarkan diskusi mengenai keamanan data rahasia perusahaan, dua lapisan keamanan telah berhasil diimplementasikan:

### A. Implementasi Google reCAPTCHA v2 (Opsi A)
*   **Tujuan:** Mencegah serangan bot atau *brute-force* pada halaman login.
*   **Pengerjaan:**
    *   Menyimpan *Site Key* dan *Secret Key* di file `.env`.
    *   Menambahkan widget reCAPTCHA ke halaman `login.blade.php` dengan tema gelap (`data-theme="dark"`).
    *   Membuat validasi manual di `app/Http/Requests/Auth/LoginRequest.php` yang berkomunikasi langsung dengan API Google, tanpa bergantung pada library pihak ketiga yang tidak kompatibel dengan Laravel versi terbaru (v13).
    *   Menambahkan logika Javascript agar reCAPTCHA otomatis me-reset (*grecaptcha.reset()*) apabila percobaan login AJAX gagal.

### B. Implementasi Two-Factor Authentication / 2FA (Opsi B)
*   **Tujuan:** Memberikan perlindungan ekstra pada akun dengan meminta kode sandi sekali pakai (OTP) dari aplikasi HP (seperti Google Authenticator) setelah memasukkan password yang benar.
*   **Pengerjaan:**
    *   Menginstal library `pragmarx/google2fa-laravel` dan `bacon/bacon-qr-code`. (Sempat terjadi kendala *cache* Windows pada saat *dump-autoload*, namun berhasil diatasi secara paksa).
    *   Membuat *migration* untuk menambahkan kolom `google2fa_secret` ke tabel `users`.
    *   Membuat `TwoFactorController` untuk mengelola logika pembuatan QR Code, penyimpanan secret key, dan validasi OTP.
    *   Membuat halaman antarmuka **Pengaturan 2FA** di dalam panel Admin (lengkap dengan panduan pemindaian QR Code) dan halaman **Verifikasi OTP** saat login.
    *   Membuat *middleware* `Check2FA` untuk memblokir akses ke dashboard admin apabila user mengaktifkan 2FA namun belum memasukkan kode OTP pada sesi tersebut.

## 3. Catatan & Rekomendasi Tambahan
*   **Penggunaan Email Admin:** Sempat dibahas mengenai penggunaan email fiktif ("bodong") seperti `admin@pim.co.id`. Secara teknis hal ini tidak mengganggu berjalannya fitur 2FA (karena email hanya dipakai sebagai label nama di dalam aplikasi Authenticator). Namun, sangat disarankan untuk menggantinya dengan format email yang lebih realistis (seperti `admin.jpp@pim.co.id`) agar aplikasi terlihat lebih profesional di mata pembimbing atau perusahaan. 
