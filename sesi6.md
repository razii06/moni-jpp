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

## File yang Diperbarui pada Sesi Ini:
- [`resources/views/components/admin-layout.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/components/admin-layout.blade.php) *(Google Font Inter, penyesuaian Tailwind, dan pembersihan CSS transform containing block)*
- [`resources/views/admin/system_logs/_table.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/system_logs/_table.blade.php) *(Redesain tabel audit log, tinggi baris konsisten, format waktu 1 baris, badge soft rounded)*
- [`resources/views/admin/system_logs/index.blade.php`](file:///c:/laragon/www/moni-jpp/resources/views/admin/system_logs/index.blade.php) *(Header breadcrumbs ringkas, slide-over filter drawer, teleportasi modal body x-teleport, scroll lock, dan escape handler)*
- [`sesi6.md`](file:///c:/laragon/www/moni-jpp/sesi6.md) *(Dokumen rangkuman sesi ini)*
