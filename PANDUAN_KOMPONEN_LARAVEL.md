# Panduan Komponen Laravel: MauJajan

Dokumen ini menjelaskan konsep arsitektur **MVC**, fungsi setiap komponen di proyek **MauJajan**, fungsi `@csrf` dan simbol-simbol Blade lainnya, serta bagaimana seluruh bagian saling bekerja sama (korelasi).

---

## 1. Apa itu MVC (Model - View - Controller)?

**MVC** adalah pola arsitektur software yang membagi aplikasi menjadi **3 bagian utama** berdasarkan tugasnya masing-masing (*Separation of Concerns*).

```text
               ┌───────────────┐
               │    BROWSER    │
               │   (Pengguna)  │
               └───────┬───────┘
                       │ 1. Request URL
                       ▼
               ┌───────────────┐
               │  CONTROLLER   │ ◄─── (Otak Logika)
               └──────┬──┬─────┘
     2. Minta Data    │  │   4. Kirim Data ke Tampilan
    ┌─────────────────┘  └───────────────────┐
    ▼                                        ▼
┌───────────────┐                    ┌───────────────┐
│     MODEL     │                    │     VIEW      │
│ (Data & DB)   │                    │ (Tampilan UI) │
└───────┬───────┘                    └───────┬───────┘
        │                                    │
   Tabel Database                     Muncul di Layar
```

### Mengapa Harus Menggunakan MVC? (Bukan 1 File Campur Aduk)
Pada PHP native jaman dulu, biasanya kode HTML, CSS, logika PHP, query database (`SELECT * FROM ...`), dan validasi dijadikan satu di dalam 1 file panjang. Akibatnya:
- Kode menjadi sangat berantakan ("Spaghetti Code").
- Jika ada error di database, tampilan web bisa rusak.
- Programmer frontend dan backend susah bekerja sama karena mengedit file yang sama.

### Dengan MVC:
1. **M (Model) $\rightarrow$ Fokus ke DATA**
   - Hanya berurusan dengan database (tabel, relasi, simpan, ambil data).
2. **V (View) $\rightarrow$ Fokus ke TAMPILAN**
   - Hanya berurusan dengan apa yang dilihat user (HTML, CSS, Blade, tombol, modal popup). Tidak ada logika bisnis rumit di sini.
3. **C (Controller) $\rightarrow$ Fokus ke LOGIKA & PENGHUBUNG**
   - Mengambil data dari Model, memprosesnya (misal: menghitung diskon/total bayar), lalu menyerahkan data tersebut ke View.

---

## 2. Analogi Sederhana: "Restoran MauJajan"

Agar mudah dibayangkan, alur kerja aplikasi Laravel persis seperti operasional restoran nyata:

| Komponen Laravel | Peran di Restoran | Tugas Nyata |
| :--- | :--- | :--- |
| **Route (`web.php`)** | **Papan Petunjuk / Pintu Masuk** | Mengarahkan tamu ke meja/bagian yang tepat sesuai URL yang dikunjungi. |
| **Controller** | **Pelayan (Pramusaji)** | Menerima pesanan pelanggan, memeriksa kelengkapan, meminta makanan ke dapur, lalu mengantarkan hasil ke meja. |
| **Model** | **Koki & Petugas Gudang** | Mengambil bahan mentah dari rak penyimpanan (Database) sesuai instruksi pelayan. |
| **View (`Blade`)** | **Buku Menu & Piring Saji** | Tampilan visual yang dinikmati oleh pelanggan (warna, tombol, daftar menu). |
| **Migration** | **Denah Rak Gudang** | Kerangka pembuatan lemari/tabel database baru secara terstruktur. |
| **Seeder** | **Stok Barang Awal Toko** | Mengisi rak gudang dengan data awal/bawaan (misal: menu standar & akun admin). |

---

## 3. Komponen Utama di Proyek MauJajan

### A. Route (`routes/web.php`)
* **Fungsi**: Gerbang utama penentu arah URL.
* **Di proyek ini**:
  - `Route::get('/', ...)` $\rightarrow$ Halaman katalog menu pelanggan.
  - `Route::post('/checkout', ...)` $\rightarrow$ Jalur submit pesanan pelanggan.
  - `Route::get('/dashboard', ...)` $\rightarrow$ Jalur dashboard admin (dilindungi login).
  - `Route::resource('/admin/foods', ...)` $\rightarrow$ Membuat 7 rute CRUD menu otomatis.

### B. Controller (`app/Http/Controllers/`)
* **Fungsi**: Otak logika aplikasi.
* **Di proyek ini**:
  - `FoodController.php`: Menangani CRUD menu makanan (tambah, upload gambar, edit, hapus).
  - `OrderController.php`: Menangani checkout pesanan pembeli, transaksi database, dan rekap pesanan di dashboard admin.

### C. Model (`app/Models/`)
* **Fungsi**: Penghubung kode PHP dengan tabel database via Eloquent ORM.
* **Di proyek ini**:
  - `Food.php`: Mengelola data menu di tabel `food`.
  - `Order.php`: Mengelola pesanan utama di tabel `orders`.
  - `OrderDetail.php`: Mengelola rincian makanan yang dibeli di tabel `order_details`.
  - `User.php`: Mengelola akun login admin di tabel `users`.

### D. View (`resources/views/`)
* **Fungsi**: Antarmuka tampilan (UI) berbasis template engine Blade.
* **Di proyek ini**:
  - `customer/index.blade.php`: Halaman katalog menu + modal konfirmasi checkout.
  - `dashboard.blade.php`: Halaman rekapitulasi pesanan untuk admin.
  - `admin/foods/`: Halaman manajemen menu makanan untuk admin.

### E. Migration (`database/migrations/`)
* **Fungsi**: Cetak biru (*blueprint*) skema tabel database yang ditulis menggunakan kode PHP, sehingga database bisa di-generate ulang dengan perintah `php artisan migrate`.

#### Mengapa Harus Pakai Migration Ketimbang Buat Tabel Manual di phpMyAdmin / GUI Lokal?
Banyak pemula tergoda langsung klik-klik di phpMyAdmin untuk membuat database dan tabel. Namun dalam standar industri dan uji kompetensi, **cara manual sangat dihindari** karena alasan berikut:
1. **Version Control untuk Database (Tercatat di Git)**: File migration adalah kode PHP. Setiap perubahan kolom, indeks, atau tabel baru tercatat dalam riwayat Git (`git commit`), sehingga kita tahu siapa yang mengubah struktur database dan kapan perubahan itu terjadi.
2. **Kolaborasi Tim yang Mulus**: Saat bekerja dalam tim, teman kita tidak perlu repot membuat tabel secara manual di laptopnya. Teman cukup melakukan `git pull` lalu mengetik `php artisan migrate`, dan struktur databasenya akan 100% identik tanpa risiko salah ketik (*human error*).
3. **Kemudahan Deploy ke Server / Hosting**: Saat aplikasi MauJajan diunggah ke server hosting atau cloud, kita tidak perlu repot ekspor-impor file `.sql` manual yang rawan error beda versi. Di server, kita cukup jalankan `php artisan migrate`.
4. **Fitur Rollback & Reset Instan**: Jika kita salah mendesain struktur tabel, kita bisa membatalkannya dengan `php artisan migrate:rollback`, atau mengulang bersih database dari awal beserta data seedernya dalam 2 detik:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. **Database-Agnostic**: Kode migration menggunakan sintaks universal (`$table->string('name')`). Kode ini dapat langsung berjalan di MySQL, PostgreSQL, SQLite, maupun SQL Server tanpa perlu mengubah query SQL manualnya.

### F. Seeder (`database/seeders/`)
* **Fungsi**: Pengisi data awal otomatis.
* **Di proyek ini**:
  - `DatabaseSeeder.php`: Membuat user admin default (`admin@gmail.com`).
  - `FoodSeeder.php`: Memasukkan 5 menu default (Nasi Goreng, Mie Goreng, Es Teh, dll.).

---

## 4. Penjelasan Mendalam `@csrf` dan Directive Blade Lainnya

Di dalam file-file tampilan (`.blade.php`), kita sering melihat simbol yang diawali tanda `@` atau kurung kurawal ganda `{{ }}`. Berikut fungsi lengkapnya:

### A. Fungsi `@csrf` (Cross-Site Request Forgery)
* **Apa itu?**  
  `@csrf` adalah fitur keamanan otomatis dari Laravel untuk melindungi formulir pengiriman data (`<form>`).
* **Mengapa Wajib Dipasang?**  
  Tanpa `@csrf`, website rentan diserang peretas menggunakan teknik **CSRF**. Peretas bisa membuat tautan atau website jebakan yang diam-diam mengirimkan formulir berbahaya ke website kita atas nama pengguna yang sedang login (misal: diam-diam mengubah status pesanan atau menghapus menu).
* **Bagaimana Cara Kerjanya?**  
  Ketika kamu menulis `@csrf`, Laravel akan menyisipkan sebuah input tersembunyi yang berisi token rahasia:
  ```html
  <input type="hidden" name="_token" value="abc123tokenAcakUnik...">
  ```
  Token ini seperti **tiket gelang resmi**:
  1. Saat form dibuka, Laravel memberikan tiket rahasia ke browser user.
  2. Saat tombol kirim diklik, tiket ikut terkirim ke Controller.
  3. Laravel mencocokkan tiket tersebut. Jika cocok, data diproses. Jika form dikirim dari pihak asing (tanpa tiket resmi), Laravel akan langsung menolaknya dengan error **`419 | Page Expired`**.

---

### B. Fungsi `@method('PATCH')` atau `@method('DELETE')` (*Method Spoofing*)
* **Masalahnya**: Standar HTML pada browser di seluruh dunia **hanya mengenal dua method**, yaitu `GET` (ambil halaman/data) dan `POST` (kirim data). Browser **tidak mendukung** penulisan `<form method="PATCH">` atau `<form method="DELETE">` secara langsung. Jika dipaksakan, browser akan otomatis menganggapnya sebagai `GET`.
* **Solusinya (Method Spoofing)**: Laravel menyediakan directive `@method(...)`:
  ```blade
  <form action="{{ route('foods.update', $food->id) }}" method="POST">
      @csrf
      @method('PATCH')
      
      <!-- Input form perubahan -->
      <button type="submit">Simpan Perubahan</button>
  </form>
  ```
  Di balik layar, Laravel mengubah `@method('PATCH')` menjadi input tersembunyi:
  ```html
  <input type="hidden" name="_method" value="PATCH">
  ```
  Saat form dikirimkan via `POST`, router Laravel akan membaca nilai `_method` tersebut dan meneruskannya ke rute `Route::patch(...)`.

#### Kenapa Harus Mengubah POST Menjadi PATCH / PUT?
Dalam standar arsitektur web modern (**RESTful API**), setiap kata kerja HTTP (*HTTP Verbs*) memiliki fungsi spesifik agar sistem terstandarisasi dan mudah dipahami:

| Method HTTP | Fungsi Utama | Contoh Aksi di MauJajan |
| :--- | :--- | :--- |
| **`GET`** | **Melihat / Mengambil** data tanpa mengubah apapun. | Menampilkan daftar menu makanan (`/admin/foods`). |
| **`POST`** | **Membuat data baru** (*Create*). | Menambahkan menu makanan baru (`store`). |
| **`PUT`** | **Mengganti total** data yang sudah ada (*Replace* seluruh kolom). | Mengganti seluruh data menu (nama, kategori, harga, gambar sekaligus). |
| **`PATCH`** | **Memperbarui sebagian** data yang ada (*Partial Update*). | Hanya mengubah harga menu atau mengubah status pesanan (`selesai`). |
| **`DELETE`** | **Menghapus** data. | Menghapus menu makanan (`destroy`). |

**Keuntungan Membedakannya:**
1. **Kerapian Route di `web.php`**: URL yang sama bisa memiliki perilaku berbeda tergantung method-nya:
   - `POST /foods` $\rightarrow$ Membuat menu baru.
   - `PATCH /foods/{id}` $\rightarrow$ Mengupdate menu yang ada.
   - `DELETE /foods/{id}` $\rightarrow$ Menghapus menu.
2. **Kesesuaian dengan Laravel Resource Controller**: Laravel secara otomatis memetakan aksi edit/update ke method `PUT`/`PATCH` saat kita memakai `Route::resource('foods', FoodController::class)`.

---

### C. Fungsi `{{ route('nama.rute') }}`
* **Fungsi**: Menghasilkan alamat URL lengkap secara dinamis berdasarkan nama rute yang didaftarkan di `web.php`.
* **Contoh**:
  - Menulis: `<a href="{{ route('foods.create') }}">Tambah</a>`
  - Hasil di HTML: `<a href="http://localhost:8000/admin/foods/create">Tambah</a>`
* **Keuntungannya**: Jika suatu hari kita mengubah alamat URL di `web.php` (misal dari `/admin/foods/create` menjadi `/admin/menu/baru`), kita **tidak perlu** mengedit file Blade satu per satu. Semua link akan otomatis menyesuaikan diri.

---

### D. Fungsi `{{ asset('storage/' . $food->image) }}`
* **Fungsi**: Mengarahkan URL file statis (gambar, CSS, file JS) ke folder `public/` web secara tepat.
* **Keuntungannya**: Memastikan file gambar tetap muncul meskipun aplikasi dipindahkan ke hosting/server yang berbeda domain.

---

### E. Fungsi Notifikasi: `session('success')` & `session('error')`
* **Fungsi**: Menampilkan **Flash Message** (pesan kilat satu kali tayang) yang dikirim oleh Controller setelah suatu aksi berhasil/gagal.
* **Contoh**:
  - Di Controller: `return back()->with('success', 'Menu berhasil dihapus!');`
  - Di Blade:
    ```blade
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    ```
  - Pesan ini hanya muncul sekali. Jika halaman di-refresh, pesan akan otomatis hilang dengan sendirinya.

---

### F. Fungsi Validasi Error: `$errors->any()` & `$errors->all()`
* **Fungsi**: Menangkap pesan kesalahan jika ada data form yang tidak memenuhi syarat validasi di Controller (misal: harga bernilai minus atau nama menu dikosongkan).
* **Contoh**:
  ```blade
  @if($errors->any())
      @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
      @endforeach
  @endif
  ```

---

### G. Perbedaan `@foreach` dan `@forelse ... @empty`
* **`@foreach`**: Melakukan perulangan biasa pada sekumpulan data.
* **`@forelse ... @empty`**: Perulangan cerdas khas Laravel. Jika ada data, data akan di-loop. Tetapi jika database masih kosong, bagian `@empty` akan otomatis dijalankan untuk menampilkan teks seperti *"Belum ada pesanan masuk"*.

---

### H. Fungsi `compact('foods')` di Controller
* **Fungsi**: Cara cepat mengirim variabel PHP ke file View.
* `compact('foods')` adalah singkatan praktis dari `['foods' => $foods]`.

---

## 5. Istilah-Istilah Penting Lainnya di Laravel

### 1. Eloquent ORM (Object-Relational Mapping)
* Fitur yang mengubah baris database menjadi objek PHP yang mudah dipakai.
* Tanpa Eloquent: `SELECT * FROM food WHERE id = 1`
* Dengan Eloquent: `Food::find(1)` atau `Food::all()`

### 2. Relasi Database (Relationships)
* **`hasMany`**: 1 Pesanan (`Order`) punya banyak rincian item (`OrderDetail`).
* **`belongsTo`**: 1 rincian item (`OrderDetail`) merujuk ke 1 menu makanan (`Food`).

### 3. `$fillable` vs `$guarded` (Keamanan Mass Assignment)
* **`$fillable`**: Menentukan kolom mana saja yang **BOLEH** diisi pengguna.
* **`$guarded`**: Menentukan kolom yang **DILARANG** diisi pengguna (misal: `protected $guarded = ['id'];` artinya kolom `id` terlindungi, sisanya aman untuk diisi).

### 4. Database Transaction (`DB::beginTransaction()`)
* Pengaman saat menjalankan serangkaian proses simpan data yang saling bergantung.
* Jika di tengah jalan ada error (misal saat menyimpan item ke-3), `DB::rollBack()` akan membatalkan semua query sebelumnya agar database tidak menyimpan data cacat.
* Jika semua berhasil, `DB::commit()` akan menyimpan data secara permanen.

### 5. Middleware (Contoh: `middleware('auth')`)
* "Satpam" penjaga halaman web. Jika pengunjung belum login mencoba membuka `/admin/dashboard`, satpam middleware akan otomatis mengusir dan mengarahkannya ke halaman login (`/login`).

### 6. Eager Loading (`with('orderDetails.food')`)
* Mengambil data pesanan sekaligus data relasi makanan dalam 1 kali query hemat, sehingga aplikasi tidak lambat (*mencegah N+1 Problem*).

---

## 6. Rangkuman Korelasi (Alur Lengkap Pemesanan)

```text
[1. User Buka Web] ──► [2. Route web.php] ──► [3. OrderController::index]
                                                      │
                                                      ▼
[5. Halaman customer/index] ◄── [4. Food Model (Ambil data dari DB)]
         │
         │ (User isi nama, meja, pilih makanan & klik pesan)
         ▼
[6. Form POST /checkout + @csrf]
         │
         ▼
[7. Route web.php] ──► [8. OrderController::store]
                              │ (DB Transaction)
                              ├──► Validasi Input ($request->validate)
                              ├──► Order::create()
                              ├──► OrderDetail::create() (looping per item)
                              ├──► DB::commit()
                              ▼
[9. Redirect ke halaman utama dengan pesan sukses session('success')]
         │
         ▼
[10. Admin membuka /dashboard] ──► [11. Dicek oleh Middleware 'auth']
                                          │
                                          ▼
[12. Tampil di dashboard.blade.php via Order::with('orderDetails.food')]
```
