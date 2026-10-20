# 🍽️ MauJajan - Panduan Cepat

Sistem pemesanan makanan dan manajemen menu (CRUD) berbasis **Laravel 11**.

---

## ⚡ Cara Menjalankan Aplikasi

Jalankan perintah berikut di terminal secara berurutan:

```bash
# 1. Install dependensi
composer install
npm install

# 2. Siapkan file environment & key
cp .env.example .env
php artisan key:generate

# 3. Migrasi database & isi data awal (seeder)
php artisan migrate --seed

# 4. Buat tautan storage (agar gambar menu muncul)
php artisan storage:link

# 5. Jalankan server (buka 2 terminal)
php artisan serve    # Terminal 1 -> http://localhost:8000
npm run dev          # Terminal 2 -> Asset compiler
```

---

## 🔐 Akun Login Admin

- **URL Login**: `http://localhost:8000/login`
- **Email**: `admin@gmail.com`
- **Password**: `password123`

---

## 📌 Alur Fitur & CRUD

### 1. Sisi Pelanggan (`http://localhost:8000/`)
* **[Read] Katalog Menu**: Pelanggan dapat melihat daftar menu tanpa login.
* **[Create] Pesan Makanan**: Isi Nama & Nomor Meja, tentukan jumlah porsi menu, lalu klik **Checkout/Pesan**.

---

### 2. Sisi Admin (`http://localhost:8000/login`)
Login menggunakan akun admin untuk mengelola:

* **Pesanan Masuk (`/admin/dashboard`)**:
  * **[Read]**: Melihat daftar pesanan pelanggan, nomor meja, dan total harga.
  * **[Update]**: Mengubah status pesanan (*Pending* -> *Diproses* -> *Selesai* / *Dibatalkan*).

* **Menu Makanan (`/admin/foods`)**:
  * **[Read]**: Melihat tabel seluruh menu makanan, kategori, harga, dan gambar.
  * **[Create]**: Tambah menu baru beserta upload foto menu.
  * **[Update]**: Edit data menu & ganti foto makanan.
  * **[Delete]**: Hapus menu (file gambar otomatis terhapus dari server).

* **Kelola User (`/admin/users`)**:
  * **[Read]**: Melihat daftar seluruh pengguna/admin beserta role.
  * **[Create]**: Tambah akun pengguna/admin baru dengan hashing password.
  * **[Update]**: Edit data akun pengguna/admin & update password.
  * **[Delete]**: Hapus akun pengguna (dilengkapi proteksi hapus diri sendiri).

---

## ⌨️ Panduan Snippets Cepat (Uji Kompetensi / Live Coding)

Gunakan shortcut ini di VS Code untuk men-generate kode secara cepat dan modular satu per satu:

### 1. Urutan Pembuatan Food Controller (`FoodController.php`):
Ketik prefix berikut secara berurutan di dalam class controller:
1. `jwp-fc-index` ➜ Fungsi `index()` (Tampil daftar menu + pagination).
2. `jwp-fc-create` ➜ Fungsi `create()` (Tampil form tambah menu).
3. `jwp-fc-store` ➜ Fungsi `store()` (Validasi input + upload foto ke storage + simpan DB).
4. `jwp-fc-edit` ➜ Fungsi `edit()` (Tampil form edit menu berdasarkan ID/Model).
5. `jwp-fc-update` ➜ Fungsi `update()` (Validasi + hapus foto lama jika ada upload baru + update data).
6. `jwp-fc-destroy` ➜ Fungsi `destroy()` (Hapus file foto dari disk storage + hapus data DB).

*(Catatan: Jika ingin membuat seluruh controller langsung sekaligus dalam 1 ketikan, gunakan `jwp-fc-all`)*.

### 2. Urutan Pembuatan Order Controller (`OrderController.php`):
Ketik prefix berikut secara berurutan di dalam class `OrderController`:
1. `jwp-oc-index` ➜ Fungsi `index()` (Tampil katalog menu untuk pemesanan pelanggan).
2. `jwp-oc-store` ➜ Fungsi `store()` (Checkout transaksi multi-tabel dengan `DB::beginTransaction()` + looping rincian pesanan).
3. `jwp-oc-dashboard` ➜ Fungsi `adminDashboard()` (Tampil rekap semua pesanan masuk admin dengan Eager Loading relasi).
4. `jwp-oc-status` ➜ Fungsi `updateStatus()` (Ubah status pesanan: Pending / Diproses / Selesai / Dibatalkan).

*(Catatan: Jika ingin membuat seluruh OrderController utuh sekaligus, gunakan `jwp-oc-all`)*.

### 3. Urutan Pembuatan View Blade (`resources/views/admin/foods/`):
- Di `index.blade.php` ➜ Ketik `jwp-v-index` (Tabel data lengkap + foto thumbnail + pagination + tombol edit & hapus).
- Di `create.blade.php` ➜ Ketik `jwp-v-create` (Form tambah lengkap dengan `enctype="multipart/form-data"` + input gambar).
- Di `edit.blade.php` ➜ Ketik `jwp-v-edit` (Form edit dengan `@method('PUT')` + preview value lama).

---

## 💡 Catatan
Jika gambar menu tidak tampil di browser, pastikan sudah menjalankan:
```bash
php artisan storage:link
```
