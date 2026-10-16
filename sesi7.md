# Sesi 7: Visibilitas Audit 2FA Pengguna, Metrik Kepatuhan Keamanan, & Audit Stabilitas Sistem Menyeluruh

Dokumen ini berisi rangkuman pekerjaan, analisis kebutuhan keamanan informasi, keputusan desain antarmuka (UI/UX), perbaikan titik kesalahan (*bug hardening*), dan pengujian fungsionalitas menyeluruh untuk proyek **Monitoring JPP (PT Pupuk Iskandar Muda)**.

---

## 1. Latar Belakang & Masalah (*Problem Statement*)

### Kebutuhan Keamanan Departemen:
- Departemen JPP mengelola data dan berkas proyek penting serta rahasia perusahaan. Fitur *Two-Factor Authentication* (2FA berbasis TOTP Google Authenticator) sebelumnya telah diimplementasikan pada Sesi 3 untuk melindungi akun.
- **Masalah Visibilitas Admin:** Admin sistem sebelumnya tidak memiliki tempat atau alat bantu untuk memantau akun siapa saja yang sudah mengaktifkan 2FA dan siapa yang belum mengaktifkannya. Halaman "Pengaturan 2FA" di sidebar hanya berfungsi *self-service* (khusus untuk akun admin yang sedang login).
- **Masalah Lapangan (*User Lockout*):** Pengguna sering kali mengalami kendala seperti HP hilang, ganti perangkat baru, atau aplikasi Authenticator terhapus tanpa backup. Tanpa intervensi admin, pengguna tersebut terkunci selamanya dan tidak bisa login ke dalam sistem.

---

## 2. Solusi & Desain Fitur yang Dieksekusi

### A. Widget Ringkasan Kepatuhan 2FA (*Security Compliance Metric*)
- Ditempatkan di header kartu **Daftar Akun Terdaftar** pada halaman [User Management](file:///c:/laragon/www/moni-jpp/resources/views/admin/users/index.blade.php).
- Memiliki indikator titik berkedip (*pulsing status dot*) dinamis yang berubah warna sesuai persentase kepatuhan departemen:
  - Hijau emerald: $\ge 80\%$
  - Amber kuning: $50\% - 79\%$
  - Merah: $< 50\%$
- Menampilkan metrik kepatuhan instan, misalnya: `Proteksi 2FA: 3/6 (50%)`.

### B. Kolom Baru: "Status 2FA" pada Tabel Akun
- Menambahkan kolom status di antara kolom **Role** dan **Aksi**.
- **Jika 2FA Aktif:** Badge hijau emerald melengkung (`bg-emerald-50 text-emerald-700 border-emerald-200`) dengan ikon perisai centang `🛡️ Aktif`.
- **Jika 2FA Nonaktif:** Badge abu-abu netral (`bg-slate-100 text-slate-500 border-slate-200`) dengan ikon gembok `⚪ Nonaktif`.

### C. Fitur Tombol Darurat: "Reset 2FA Pengguna" oleh Admin
- Ditambahkan ke kolom **Aksi** khusus untuk pengguna lain yang telah mengaktifkan 2FA.
- Dilengkapi dengan ikon kunci/gembok kuning amber dan dialog konfirmasi modal enterprise (*SweetAlert2*) tipe peringatan (*warning*):
  - *Title:* "Reset 2FA Pengguna?"
  - *Target:* Nama dan username pengguna bersangkutan.
  - *Pesan:* "Kunci otentikasi 2FA pengguna ini akan dihapus. Pengguna dapat login kembali tanpa verifikasi OTP dan dapat melakukan setup ulang jika diinginkan."
- **Pencatatan Audit Trail:** Setiap tindakan reset oleh admin secara otomatis dicatat ke dalam `SystemLog` dengan aksi `RESET 2FA USER` agar akuntabel dan terlacak dalam jejak audit departemen.

---

## 3. Audit Stabilitas & Perbaikan Titik Error (*System Hardening*)

Setelah pengujian fungsionalitas menyeluruh (*exhaustive diagnostic testing*), ditemukan beberapa titik potensi kesalahan krusial yang langsung diperbaiki:

### A. Pencegahan *Crash 500 Uncaught Exception* pada Verifikasi 2FA
- **Temuan Error:** Library `PragmaRX\Google2FA` melempar `SecretKeyTooShortException` (Crash 500) jika endpoint verifikasi OTP diakses/diposting oleh pengguna yang belum memiliki *secret key* atau saat secret terformat rusak.
- **Solusi:**
  - Menambahkan proteksi redirect pada `showVerifyForm()` dan `verify()` di [`TwoFactorController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/Auth/TwoFactorController.php): jika user belum mengaktifkan 2FA atau sudah lolos verifikasi pada sesi tersebut, otomatis dialihkan ke dashboard.
  - Membungkus eksekusi `verifyKey()` ke dalam blok `try-catch (\Throwable $e)` sehingga input OTP yang salah atau secret yang korup tidak pernah merusak server.

### B. Resolusi Kerapuhan Konfigurasi Produksi reCAPTCHA (*Config Cache Vulnerability*)
- **Temuan Error:** Pada [`LoginRequest.php`](file:///c:/laragon/www/moni-jpp/app/Http/Requests/Auth/LoginRequest.php) dan [`login.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/auth/login.blade.php), pemanggilan API key menggunakan fungsi langsung `env('RECAPTCHA_SECRET_KEY')`. Pada server produksi Laravel yang menerapkan optimasi `php artisan config:cache`, pemanggilan `env()` di luar folder `config/` akan mengembalikan `null`, yang berakibat **semua user terkunci dan tidak dapat login sama sekali**.
- **Solusi:**
  - Mendaftarkan entri konfigurasi `recaptcha` pada [`config/services.php`](file:///c:/laragon/www/moni-jpp/config/services.php).
  - Mengarahkan `LoginRequest` dan `login.blade.php` untuk membaca melalui `config('services.recaptcha.secret_key')` dan `config('services.recaptcha.site_key')` dengan fallback `env()`.

### C. Penanganan Error Tanggal pada Dashboard Tren (*Invalid Date Exception*)
- **Temuan Error:** Pada [`DashboardController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/DashboardController.php), query string `start_date` dan `end_date` yang dikirim dengan format acak atau salah memicu `Carbon\Exceptions\InvalidFormatException` (Crash 500).
- **Solusi:**
  - Membungkus parsing tanggal ke dalam mekanisme `try-catch` dengan fallback aman ke awal dan akhir tahun berjalan.
  - Memastikan jika `start_date > end_date`, sistem secara otomatis menukar (*swap*) kedua tanggal agar rentang tetap valid.
  - Memvalidasi parameter `group_by` agar hanya menerima nilai aman (`daily`, `weekly`, `monthly`).

### D. Perbaikan Pengalaman Pengguna (*Graceful Handling*) pada Unduh Dokumen
- **Temuan Error:** Pada [`JobPackageController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/JobPackageController.php), method `downloadDocument` sebelumnya menggunakan `abort(404, ...)` saat dokumen atau berkas fisik tidak ada di disk lokal, melempar user ke halaman error HTTP mentah.
- **Solusi:**
  - Mengganti `abort(404)` dengan `return back()->with('error', '...')`, sehingga SweetAlert2 memunculkan toast/dialog ramah pengguna tanpa meninggalkan halaman detail paket pekerjaan.

### E. Penyelarasan Pengecekan Disk Google Drive
- **Temuan:** Penggunaan `env('FILESYSTEM_DISK') === 'google'` pada method `destroy` dan `deleteDocument` diganti menjadi standar konfigurasi Laravel: `config('filesystems.default') === 'google'`.

---

## 4. Berkas yang Diperbarui

1. **[`app/Models/User.php`](file:///c:/laragon/www/moni-jpp/app/Models/User.php)**
   - Menambahkan method pembantu: `hasTwoFactor(): bool`.

2. **[`app/Http/Controllers/Admin/UserController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/Admin/UserController.php)**
   - Menghitung statistik kepatuhan 2FA dan menangani `reset2FA(User $user)`.

3. **[`routes/web.php`](file:///c:/laragon/www/moni-jpp/routes/web.php)**
   - Mendaftarkan route: `POST admin/users/{user}/reset-2fa`.

4. **[`resources/views/admin/users/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/users/index.blade.php)**
   - Menambahkan widget kepatuhan 2FA, kolom Status 2FA, dan tombol Reset 2FA.

5. **[`app/Http/Controllers/Auth/TwoFactorController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/Auth/TwoFactorController.php)**
   - Memperkuat validasi secret key, pengalihan otomatis, dan penanganan exception `verifyKey`.

6. **[`config/services.php`](file:///c:/laragon/www/moni-jpp/config/services.php)**
   - Menambahkan konfigurasi resmi `recaptcha` (`site_key` & `secret_key`).

7. **[`app/Http/Requests/Auth/LoginRequest.php`](file:///c:/laragon/www/moni-jpp/app/Http/Requests/Auth/LoginRequest.php)**
   - Membaca kredensial reCAPTCHA melalui `config()` untuk mendukung `config:cache`.

8. **[`resources/views/auth/login.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/auth/login.blade.php)**
   - Membaca `sitekey` reCAPTCHA dari konfigurasi resmi.

9. **[`app/Http/Controllers/DashboardController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/DashboardController.php)**
   - Menambahkan sanitasi tanggal dan proteksi rentang tren beban kerja dari crash 500.

10. **[`app/Http/Controllers/JobPackageController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/JobPackageController.php)**
    - Memperbaiki penanganan unduh berkas yang hilang dengan pesan error elegan (*SweetAlert2*), serta merapikan pengecekan disk storage.

11. **[`resources/views/admin/job_packages/show.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/job_packages/show.blade.php)**
    - Penyelarasan tipografi: Menghapus kelas `font-mono` yang bertabrakan pada No. Service Order, No. Service Notifikasi, dan Nomor PO.
    - Menerapkan utilitas OpenType `tabular-nums` dan menyelaraskan ke font korporat global **Inter** (`text-slate-800`), menghasilkan tata letak kartu yang 100% harmonis dan profesional.

12. **[`resources/views/admin/users/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/users/index.blade.php)**
    - Penyelarasan tipografi: Menghapus kelas `font-mono` dan warna oranye mencolok (`text-amber-600`) pada kolom **Username**.
    - Menggunakan font global **Inter** (`font-semibold text-slate-700`) dipadukan dengan simbol prefix halus `@` (`text-slate-400`), menyelaraskan visual tabel kredensial dengan standar SaaS enterprise modern.

13. **[`resources/views/admin/system_logs/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/system_logs/index.blade.php)**
    - Redesain panel filter: Mengubah slide-over drawer yang sebelumnya menempel kaku ke bibir atas browser (`y = 0`) dan memanjang kosong menjadi **Modern Floating Sheet Card** (`rounded-2xl`, margin luar `p-4 sm:p-6 md:p-8`, padding atas proporsional `pt-14 sm:pt-16 md:pt-20`, border lembut `border-slate-200/90`, dan elevasi bayangan halus `shadow-2xl shadow-slate-900/20`).
    - Menghilangkan peregangan vertikal kaku (`h-full`) sehingga kartu memiliki tinggi natural sesuai konten (`h-auto` dengan batasan `max-h-[calc(100vh-6rem)]`), mengeliminasi ruang putih kosong berlebih.
    - Posisi panel filter kini sejajar nyaman dengan batas pandang mata dan header tabel, memberikan kesan melayang yang modern, bersih, dan berstandar enterprise SaaS.

14. **Pemurnian Modul Dokumentasi Beranda (Visual Media Showcase):**
    - **[`app/Http/Controllers/Admin/DokumentasiController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/Admin/DokumentasiController.php)**:
      - Menghapus dukungan kategori `Dokumen PDF` dan membatasi validasi hanya untuk `['Foto Kegiatan', 'Tautan Video']` serta MIME type gambar (`image/jpeg,image/png,image/webp`).
      - Mengaktifkan filter pencarian nama judul dan filter tab segmented (`tab=foto` dan `tab=video`) yang sebelumnya belum ditangani di tingkat backend.
    - **[`resources/views/admin/dokumentasi/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/dokumentasi/index.blade.php)**:
      - Memperbarui subtitle dan tab filter menjadi: **Semua Media**, **Foto Kegiatan**, dan **Tautan Video**.
      - Menghapus cabang render icon PDF pada tabel daftar media dan membersihkan opsi `Dokumen PDF` dari modal form upload media.
    - **[`resources/views/public/home.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/public/home.blade.php)**:
      - Mengeliminasi blok render kotak merah ikon PDF pada *Infinite Marquee* beranda, menyelaraskan teks pengantar menjadi *"dokumentasi foto & video kegiatan"*, serta memastikan galeri publik 100% konsisten menyajikan konten visual interaktif.
    - **Basis Data (`dokumentasis`)**:
      - Membersihkan data uji coba berkas PDF (`tst`) sehingga tidak meninggalkan record yatim (*orphan records*).


