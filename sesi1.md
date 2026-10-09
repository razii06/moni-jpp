# Sesi 1: Menambahkan Gambar Latar Belakang dengan Efek Gradasi (Overlay)

## Permintaan Pengguna
Pengguna ingin menambahkan gambar latar belakang di balik gradasi warna biru pada bagian "Hero Banner" (Monitoring Progres Pekerjaan Dept. JPP). Tujuannya adalah agar gambar tetap terlihat namun tergradasi atau dilapisi dengan warna biru yang sudah ada pada kotak putih tersebut.

## Solusi
Untuk mencapai hasil ini, kita dapat menggabungkan CSS `linear-gradient` dengan `url()` pada properti `background`. Warna pada gradasi diubah formatnya menggunakan `rgba()` agar kita bisa mengatur tingkat transparansi (opacity) sehingga gambar di bawahnya tetap terlihat tembus pandang.

## Implementasi
File yang dimodifikasi adalah: `C:\laragon\www\moni-jpp\resources\views\public\home.blade.php`

Gambar latar yang digunakan: `C:\laragon\www\moni-jpp\public\images\pimlatar.png`

**Kode yang diubah:**

Dari:
```html
<!-- Hero Banner (Full-Width, Gradasi Biru ke Putih, Teks Rata Tengah) -->
<div class="animate-popup delay-100 relative py-14 sm:py-20 overflow-hidden text-center"
        style="background: linear-gradient(180deg, #0f2b5c 0%, #1b4385 65%, #f8fafc 100%);">
```

Menjadi:
```html
<!-- Hero Banner (Full-Width, Gradasi Biru ke Putih, Teks Rata Tengah) -->
<div class="animate-popup delay-100 relative py-14 sm:py-20 overflow-hidden text-center"
        style="background: linear-gradient(180deg, rgba(15, 43, 92, 0.85) 0%, rgba(27, 67, 133, 0.85) 65%, rgba(248, 250, 252, 1) 100%), url('{{ asset('images/pimlatar.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
```

**Penjelasan Modifikasi:**
1. **`url('{{ asset('images/pimlatar.png') }}')`**: Memuat gambar latar dari folder public Laravel.
2. **`rgba(15, 43, 92, 0.85)` & `rgba(27, 67, 133, 0.85)`**: Mengubah warna HEX (`#0f2b5c` & `#1b4385`) menjadi format RGB dengan _Alpha channel_ (opacity) sebesar 85% (`0.85`). Ini membuat gradasi warna biru menjadi sedikit transparan sehingga gambar terlihat.
3. **`rgba(248, 250, 252, 1)`**: Bagian paling bawah tetap warna putih solid (100% opacity) agar transisi/potongan gambar dengan elemen di bawahnya tetap rapi.
4. Menambahkan atribut `background-size: cover`, `background-position: center`, dan `background-repeat: no-repeat` agar gambar proporsional dan tidak berulang.
