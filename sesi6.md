# Sesi 6: Redesain Audit Log Standar Enterprise (Acme/Freshservice UI) & Perbaikan Stacking Context Viewport Modal

Dokumen ini berisi rangkuman pekerjaan, keputusan desain, analisis teknis, dan pembaruan antarmuka (UI/UX) yang telah berhasil dieksekusi pada hari ini untuk proyek **Monitoring JPP (PT Pupuk Iskandar Muda)**.

---

## 1. Redesain Total Halaman Audit Log (Jejak Sistem) Berdasarkan Referensi SaaS Modern

### Permasalahan:
- Tampilan tabel audit log sebelumnya terasa semrawut, padat, dan "acak-acakan".
- Menggunakan font sistem bawaan browser (*Segoe UI*), menghasilkan teks kaku dan angka yang kurang seimbang.
- Kolom *WHAT CHANGED?* dipadati oleh ikon folder besar serta tumpukan badge dokumen inline (`[RAB]`, `[Surat Permintaan]`, `[Surat Izin Prinsip]`), menyebabkan ketinggian baris bergelombang (*uneven row height*) dan tidak rata.
- Kolom *TIME* memuat 3 tingkat teks sekaligus (Tanggal, Jam WIB, dan teks waktu relatif pudar *"22 jam yang lalu"*).
- Badge status aksi (*Action*) menggunakan bentuk lonjong penuh (*capsule pill*) dengan warna terlalu mencolok yang kurang harmonis dengan standar enterprise UI.

### Solusi & Keputusan Desain:
Diterapkan transformasi penuh mengacu pada desain referensi SaaS enterprise (*Acme / Freshservice UI*):

1. **Penerapan Font Modern Enterprise (**Inter**):**
   - Mengimpor Google Font **Inter** (bobot 300, 400, 500, 600, 700) di [`admin-layout.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/components/admin-layout.blade.php) dan mengonfigurasikannya sebagai font global utama.
   - Karakter huruf memiliki *x-height* seimbang, teks tajam (*crisp rendering*), dan format angka tabular yang lurus pada jam, tanggal, dan alamat IP.
2. **Ketinggian Baris Seragam (*Uniform Row Height*):**
   - Menghilangkan tumpukan lencana berkas inline dari kolom utama. Ketinggian baris kini rata, proporsional, dan nyaman dipindai mata.
3. **Format Waktu Ringkas 1 Baris (*Time Column*):**
   - Mengubah format waktu menjadi satu baris bersih: `Tue, 13 Okt, 2026 at 11:56 WIB`. Teks detik dan waktu relatif (*diffForHumans*) dipindahkan ke popup detail.
4. **Hierarki 2 Tingkat Bersih (*What Changed? Column*):**
   - **Baris 1 (Kategori/Tipe):** Teks halus abu-abu netral (`text-xs text-slate-400 font-medium`), seperti *Job Package*, *Dokumen Berkas*, *Account*, *Security*.
   - **Baris 2 (Nama Entitas/Proyek):** Teks gelap tegas (`text-sm font-semibold text-slate-800 leading-snug`).
5. **Lencana Status Aksi Halus (*Subtle Rounded Action Badges*):**
   - Mengubah radius menjadi sudut tumpul halus (`rounded`, radius 4px) dengan palet warna pastel tenang:
     - `Updated` : Amber lembut (`bg-[#fffbeb] text-[#b45309] border-[#fde68a]`)
     - `Created` : Hijau emerald lembut (`bg-[#ecfdf5] text-[#047857] border-[#a7f3d0]`)
     - `Uploaded` : Biru lembut (`bg-[#eff6ff] text-[#1d4ed8] border-[#bfdbfe]`)
     - `Cancelled` : Mawar merah lembut (`bg-[#fff1f2] text-[#be123c] border-[#fecdd3]`)
     - `Logged in` : Teal lembut (`bg-[#f0fdfa] text-[#0f766e] border-[#99f6e4]`)
6. **Kolom Pelaku (*Performed by*):**
   - Nama pengguna semi-bold di baris atas (`font-semibold text-slate-800`), alamat IP di baris bawah dalam font monospace bersih (`text-xs text-slate-400 font-mono`).
7. **Kolom Ringkasan & Tautan Interaktif (*Details Column*):**
   - Kalimat ringkasan ramah manusia (*human-readable*) + tautan biru elegan **`View more >`** dengan ikon chevron untuk membuka popup detail.
8. **Slide-Over Drawer Filter Panel:**
   - Panel filter meluncur dari sisi kanan layar sesuai referensi:
     - Header: Judul `Filter`, tombol `Clear all`, dan tombol `X`.
     - Filter `Date Range` (dropdown pilihan tanggal/kustom).
     - Filter `What changed?` (pilihan tipe: *Account, Job Package, Dokumen Berkas, Agent, Security, Change*).
     - Filter `Performed by` dan kotak pencarian kata kunci.
     - Footer: Tombol `Cancel` dan tombol `Apply` warna dark slate/navy.

---

## 2. Perbaikan Bug Stacking Context Viewport pada Modal Detail ("View More")

### Permasalahan:
- Saat admin melakukan *scroll* ke bawah tabel (misalnya pada baris ke-14 *"Jasa Modifikasi & Pengecoran Bandwall Stopper..."*) lalu menekan tombol **`View more >`**, informasi modal detail muncul di koordinat paling atas halaman (*Y = 0*).
- Admin tidak melihat apapun di layar saat tombol ditekan dan dipaksa harus *scroll* manual jauh kembali ke bagian paling atas halaman untuk membaca detail.

### Akar Masalah Teknis (*Root Cause*):
- Pada [`admin-layout.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/components/admin-layout.blade.php), area `<main>` dibungkus dalam `div` yang memiliki kelas animasi transisi `transform` (`transform transition-all ... translate-y-0 scale-100`).
- Berdasarkan **spesifikasi W3C CSS**, elemen apapun yang memiliki properti `transform` aktif akan bertindak sebagai *containing block* baru bagi semua elemen keturunannya yang berposisi `position: fixed`.
- Akibatnya, elemen modal yang memiliki `class="fixed inset-0"` kehilangan sifat penjangkaran terhadap layar kaca monitor (*window viewport*), dan malah terkurung di dalam container halaman di posisi paling atas (*Y = 0*).

### Solusi & Keputusan Desain:
1. **Teleportasi Modal & Drawer via Alpine.js (`<template x-teleport="body">`):**
   - Membungkus Modal Detail (`detailModalOpen`) dan Slide-Over Drawer (`openFilterDrawer`) menggunakan direktif `<template x-teleport="body">` di [`index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/system_logs/index.blade.php).
   - Elemen DOM dipindahkan secara otomatis langsung menjadi anak dari tag `<body>`, bebas 100% dari batasan CSS container tabel maupun layout.
2. **Penjangkaran Sejati Tengah Layar (*True Viewport Centering*):**
   - Modal kini menggunakan `fixed inset-0 z-[9999] flex items-center justify-center`.
   - Di baris mana pun admin mengklik (baris 1, baris 14, maupun baris 100), popup modal **selalu muncul tepat di depan mata admin seketika tanpa perlu scroll 1 milimeter pun**.
3. **Pembersihan CSS Animation di Layout Utama:**
   - Menghapus pembungkus transisi `transform` permanen di [`admin-layout.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/components/admin-layout.blade.php).
   - Memperbarui animasi `@keyframes adminPagePop` agar berakhir dengan `transform: none` di 100%, mencegah efek samping serupa pada elemen `fixed` lainnya di seluruh web admin.
4. **Kenyamanan Ekstra (Body Scroll Lock & Escape Key):**
   - **Kunci Gulir Latar:** Menambahkan `this.$watch` pada Alpine untuk mengaktifkan `overflow-hidden` pada `<body>` saat modal atau drawer terbuka agar halaman belakang tidak bergulir.
   - **Tombol Pintas `Escape`:** Menambahkan listener `@keydown.escape.window` sehingga admin cukup menekan tombol `Esc` di keyboard untuk menutup modal atau drawer dengan cepat.

---

## 3. Redesain Dialog Konfirmasi Aksi Krusial (*Modern Enterprise Modal*)

### Permasalahan:
- Saat admin menekan tombol aksi hapus (ikon tong sampah), browser memunculkan kotak dialog bawaan peramban (*native JavaScript confirm dialog*).
- Dialog tersebut memunculkan teks domain URL ngrok (`hypsographical-deflectingly-lilliam.ngrok-free.dev menyatakan`), tampak kaku, tidak aman, dan seperti web mentah yang belum selesai dikembangkan.
- Tidak terdapat preview nama data yang hendak dihapus dan desain kotak abu-abu tidak selaras dengan tema UI aplikasi.

### Solusi & Keputusan Desain:
1. **Penerapan SweetAlert2 Enterprise Modal:**
   - Menghilangkan dialog `window.confirm()` bawaan browser di seluruh sistem.
   - Desain kartu modal putih melengkung elegan (*rounded-2xl*), berpadu dengan efek kaca kabur (*backdrop blur*) bernuansa slate gelap.
   - Tipografi menggunakan font **Inter** dengan ikon peringatan merah mawar (*rose-500*) yang beranimasi halus.
2. **Preview Dinamis Item yang Dihapus:**
   - Modal secara otomatis menampilkan nama pekerjaan/pengguna/dokumen yang ditargetkan dalam kotak badge abu-abu lembut (`&ldquo;Nama Item&rdquo;`) sehingga admin dapat memverifikasi sebelum menghapus.
3. **Penyelarasan Seluruh Tombol Aksi:**
   - Diterapkan pada tombol hapus Job Package, pembatalan (*cancel*), aktivasi kembali (*reactivate*), hapus akun pengguna di User Management, hapus dokumentasi beranda, nonaktifkan 2FA, hingga hapus berkas lampiran.

---

## 4. Penyelarasan Nama Role Pengguna (*Supervisor*)

### Permasalahan:
- Pada formulir pembuatan akun baru, dropdown pilihan hak akses bertuliskan **Supervisor**, namun pada tabel daftar pengguna di sebelah kanan, badge bertuliskan **SUPERVISI**.

### Solusi:
- Menyelaraskan pemetaan string role di [`resources/views/admin/users/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/users/index.blade.php) sehingga badge role menampilkan teks resmi jabatan: **SUPERVISOR** dengan warna ungu lembut yang konsisten.

---

## 5. Perbaikan Area Gelap pada Toast Notifikasi & Implementasi Smart Scroll Memory

### Permasalahan:
1. **Area Gelap di Kolom Kanan:** Saat aksi berhasil dan toast notifikasi hijau (`Job Package berhasil diaktifkan kembali`) muncul di pojok kanan atas, seluruh area kolom vertikal kanan tertutup warna hitam gelap berkabut (*backdrop blur*).
2. **Layar Mental ke Paling Atas:** Saat admin menonaktifkan (*cancel*) atau mengaktifkan kembali (*reactivate*) pekerjaan di bagian bawah tabel, halaman me-reload dan posisi pandangan admin otomatis mental ke paling atas halaman (`Y = 0`), sehingga admin harus menggulir (*scroll*) ulang ke bawah untuk melihat hasilnya.

### Solusi & Keputusan Desain:
1. **Isolasi CSS Backdrop SweetAlert2:**
   - Menambahkan aturan ketat agar efek gelap kabur (`backdrop-blur`) **hanya aktif saat dialog konfirmasi pop-up modal tengah terbuka** (`body.swal2-shown:not(.swal2-toast-shown)`).
   - Menetapkan kontainer Toast (`body.swal2-toast-shown .swal2-container`) menjadi **100% transparan tanpa blur dan bebas pointer intercept** (`pointer-events: none`). Notifikasi hijau kini melayang anggun di pojok kanan atas tanpa menggelapkan layar.
2. **Implementasi Smart Scroll Memory & Auto-Focus Anchor:**
   - **Penyimpanan Koordinat Scroll:** Sebelum form aksi dikirim ke server, sistem otomatis mencatat posisi scroll (`sessionStorage.setItem('moni_jpp_scroll_pos', window.scrollY)`).
   - **Auto-Focus Target Board:** Untuk aksi pembatalan, form diarahkan ke `data-scroll-anchor="table-cancelled"`, dan untuk aktivasi kembali ke `data-scroll-anchor="table-running"`.
   - **Restorasi Otomatis:** Begitu halaman selesai me-reload, sistem otomatis mengembalikan layar ke posisi scroll terakhir atau meluncur mulus ke tabel tujuan. Admin tidak akan pernah merasakan layar "terlempar" ke atas lagi.

---

## 6. Perbaikan Integrasi Upload Seluruh Dokumen Lampiran & Eliminasi Silent Failure (Akses via Ngrok)

### Permasalahan:
- Saat aplikasi diakses oleh rekan pengguna melalui koneksi *ngrok*, rekan pengguna mencoba mengunggah berkas dokumen pada form edit/tambah Job Package.
- Sistem menampilkan notifikasi sukses (*"Job Package berhasil diperbarui!"*), namun saat detail pekerjaan diperiksa, **berkas dokumen yang diunggah tidak tersimpan dan tidak ada di Google Drive maupun database**.

### Akar Masalah Teknis (*Root Cause*):
1. **Inkonsistensi Jumlah Kolom Dokumen (6 vs 9):**
   - Halaman antarmuka pengguna (`create.blade.php`, `edit.blade.php`, dan `show.blade.php`) menyediakan **9 slot dokumen**:
     - Dokumen Utama: `doc_surat_permintaan`, `doc_surat_izin_prinsip`, `doc_bak`, `doc_rab`, `doc_form_pbj`, `doc_tor`, `doc_bast`, `doc_laporan_pekerjaan`.
     - Dokumen Tambahan: `doc_pendukung_lainnya`.
   - Namun, pada skema tabel MySQL `job_packages` dan properti `$docFields` di [`JobPackageController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/JobPackageController.php), sistem sebelumnya **hanya mendefinisikan 6 kolom dokumen** (`doc_rab`, `doc_bak`, `doc_surat_permintaan`, `doc_surat_izin_prinsip`, `doc_tor`, `doc_bast`).
   - Tiga kolom krusial: `doc_form_pbj`, `doc_laporan_pekerjaan`, dan `doc_pendukung_lainnya` **tidak ada di tabel database dan diabaikan oleh controller**.
2. **Kondisi *Silent Failure*:**
   - Metode `handleFileUploads()` hanya melakukan iterasi pada `$this->docFields`. Karena 3 jenis dokumen tersebut tidak terdaftar, berkas yang dikirimkan oleh browser diabaikan sepenuhnya.
   - Controller tetap mengeksekusi *update* data teks biasa dan kembali dengan pesan sukses tanpa memberi tahu pengguna bahwa berkas lampirannya tidak diproses.

### Solusi & Keputusan Desain:
1. **Migrasi Database Penambahan 3 Kolom Dokumen:**
   - Menjalankan migrasi [`2026_10_15_120000_add_remaining_document_columns_to_job_packages_table.php`](file:///c:/laragon/www/moni-jpp/database/migrations/2026_10_15_120000_add_remaining_document_columns_to_job_packages_table.php).
   - Menambahkan kolom bertipe `TEXT NULL` untuk `doc_form_pbj`, `doc_laporan_pekerjaan`, dan `doc_pendukung_lainnya` yang siap menyimpan array JSON tautan Google Drive / penyimpanan publik.
2. **Sinkronisasi Model & Form Request:**
   - **Model [`JobPackage.php`](file:///c:/laragon/www/moni-jpp/app/Models/JobPackage.php):** Memasukkan ketiga kolom ke `$fillable` dan mendaftarkannya pada `$casts` sebagai `'array'`.
   - **Form Requests ([`StoreJobPackageRequest.php`](file:///c:/laragon/www/moni-jpp/app/Http/Requests/StoreJobPackageRequest.php) & [`UpdateJobPackageRequest.php`](file:///c:/laragon/www/moni-jpp/app/Http/Requests/UpdateJobPackageRequest.php)):** Menambahkan aturan validasi berkas (`file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:20480`) dan label bahasa Indonesia yang ramah.
3. **Penyempurnaan Controller & Penanganan Error Upload Terpusat:**
   - Memperluas array `$docFields` di [`JobPackageController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/JobPackageController.php) sehingga mencakup **keseluruhan 9 dokumen**.
   - Menambahkan blok penangkap kegagalan (*error trapping*) pada `handleFileUploads()`: jika ada berkas yang korup, jaringan ngrok terputus, atau API Google Drive mengalami kendala, sistem akan mencatat detail error ke *System Log* dan memunculkan notifikasi peringatan (*Warning*) ke pengguna secara transparan.
4. **Implementasi UI Warning Dialog di Admin Layout:**
   - Menambahkan penangkap `@if(session('warning'))` pada [`admin-layout.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/components/admin-layout.blade.php) menggunakan SweetAlert2 bertema amber/kuning modern sehingga pengguna langsung mengetahui jika ada berkas yang gagal terunggah.

---

## File yang Diperbarui pada Sesi Ini:
- [`database/migrations/2026_10_15_120000_add_remaining_document_columns_to_job_packages_table.php`](file:///c:/laragon/www/moni-jpp/database/migrations/2026_10_15_120000_add_remaining_document_columns_to_job_packages_table.php) *(Migrasi penambahan kolom doc_form_pbj, doc_laporan_pekerjaan, doc_pendukung_lainnya)*
- [`app/Models/JobPackage.php`](file:///c:/laragon/www/moni-jpp/app/Models/JobPackage.php) *(Registrasi $fillable & $casts array untuk 3 kolom baru)*
- [`app/Http/Requests/StoreJobPackageRequest.php`](file:///c:/laragon/www/moni-jpp/app/Http/Requests/StoreJobPackageRequest.php) *(Validasi berkas & custom attributes 9 dokumen)*
- [`app/Http/Requests/UpdateJobPackageRequest.php`](file:///c:/laragon/www/moni-jpp/app/Http/Requests/UpdateJobPackageRequest.php) *(Validasi berkas & custom attributes 9 dokumen)*
- [`app/Http/Controllers/JobPackageController.php`](file:///c:/laragon/www/moni-jpp/app/Http/Controllers/JobPackageController.php) *(Ekspansi $docFields ke 9 dokumen, penanganan upload berkas, error trapping, flash session warning)*
- [`resources/views/components/admin-layout.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/components/admin-layout.blade.php) *(Peringatan SweetAlert2 session warning, smart scroll memory, isolasi backdrop toast, Inter font)*
- [`resources/views/admin/job_packages/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/job_packages/index.blade.php) *(Action buttons single row icon, modern confirm delete & status toggle attributes, scroll anchor)*
- [`resources/views/admin/job_packages/show.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/job_packages/show.blade.php) *(Modern confirm dialog untuk aktivasi kembali & pembatalan)*
- [`resources/views/admin/job_packages/edit.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/job_packages/edit.blade.php) *(Konfirmasi hapus berkas lampiran terintegrasi)*
- [`resources/views/admin/users/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/users/index.blade.php) *(Penyelarasan label role SUPERVISOR dan modal konfirmasi hapus akun)*
- [`resources/views/admin/dokumentasi/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/dokumentasi/index.blade.php) *(Modal konfirmasi hapus dokumentasi)*
- [`resources/views/admin/2fa_setup.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/2fa_setup.blade.php) *(Modal konfirmasi nonaktifkan 2FA)*
- [`resources/views/admin/system_logs/_table.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/system_logs/_table.blade.php) *(Redesain tabel audit log, tinggi baris konsisten, format waktu 1 baris)*
- [`resources/views/admin/system_logs/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/system_logs/index.blade.php) *(Header breadcrumbs ringkas, slide-over filter drawer, teleportasi modal body)*
- [`sesi6.md`](file:///c:/laragon/www/moni-jpp/sesi6.md) *(Dokumentasi perbaikan lengkap sesi 6)*


