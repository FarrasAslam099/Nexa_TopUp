# ⚡ Nexa_Topup - Sistem Top Up Mobile Legends Online

## 📖 Deskripsi

Nexa_Topup adalah sistem top up game berbasis web yang dirancang untuk memudahkan pemain Mobile Legends melakukan pengisian Diamond, Membership, Bundle, Weekly Pass, dan Skin Pass secara cepat, aman, dan terpercaya.

Website ini menyediakan katalog produk top up lengkap berdasarkan kategori, proses pemesanan dengan verifikasi User ID & Zone ID, berbagai metode pembayaran, riwayat transaksi pengguna, serta panel admin untuk mengelola produk dan pesanan secara menyeluruh.

## 🎯 Tujuan

* Menyediakan layanan top up Mobile Legends yang mudah diakses dan terpercaya.
* Memberikan pengalaman transaksi yang cepat, aman, dan instan.
* Menawarkan produk top up lengkap dengan harga kompetitif.
* Memudahkan pengelolaan produk dan pesanan melalui panel admin yang terstruktur.

## 👨‍💻 Tim Pengembang

### Muhammad Ikbal

**Backend Developer**

Tanggung Jawab:

* Implementasi autentikasi pengguna (Login, Register, Logout)
* Pengelolaan session dan keamanan akses

### Muhammad Fathan Abdullah

**Backend Developer**

Tanggung Jawab:

* Perancangan database MySQL
* Pengembangan sistem menggunakan PHP Native
* Pengembangan alur pemesanan (Order & Checkout)
* Pengembangan fitur riwayat transaksi
* Pengembangan dashboard admin
* Implementasi manajemen produk dan pesanan

### Lalu Farras Hanif Aslam

**Frontend Developer**

Tanggung Jawab:

* Desain antarmuka website
* Implementasi HTML, CSS, dan JavaScript
* Pengembangan tampilan responsif
* Pengembangan UI/UX website termasuk halaman hero, katalog produk, dan panel pesanan

## 👥 Aktor Sistem

### 1. Guest (Pengunjung)

Dapat melakukan:

* Melihat halaman beranda
* Melihat produk unggulan dan kategori
* Melihat halaman top up dan katalog produk
* Registrasi akun
* Login akun

### 2. User

Dapat melakukan:

* Login dan Logout
* Melakukan top up produk (Diamond, Membership, Bundle, dll.)
* Memasukkan dan memverifikasi User ID & Zone ID Mobile Legends
* Memilih metode pembayaran
* Melanjutkan ke halaman checkout dan konfirmasi pesanan
* Melihat riwayat transaksi beserta status pesanan
* Menggunakan fitur pagination pada riwayat transaksi

### 3. Admin

Dapat melakukan:

* Login Admin
* Mengakses dashboard admin
* Melihat statistik (total pesanan, pesanan hari ini, pesanan pending, total pendapatan, total user, produk aktif)
* Mengelola data produk (tambah, aktifkan/nonaktifkan, hapus)
* Mengelola pesanan (lihat, filter, ubah status, hapus)
* Melihat daftar pesanan terbaru secara real-time

## ✨ Fitur Utama

### 🔐 Autentikasi

* Login
* Register (dengan validasi username, email, password)
* Logout
* Proteksi halaman dengan session guard

### 🎮 Katalog Produk

* Tampilan beranda dengan produk unggulan (HOT, POPULER, BEST VALUE)
* Kategori produk: Diamond, Membership, Bundle, Weekly Pass, Skin Pass
* Filter produk berdasarkan kategori (tab navigasi)
* Badge produk (HOT, POPULER, BEST VALUE, SALE, PROMO, NEW)
* Countdown flash sale di halaman beranda
* Informasi harga asli dan harga promo

### 🆔 Verifikasi Akun Game

* Input User ID dan Zone ID Mobile Legends
* Fitur cek nickname akun sebelum melanjutkan pesanan

### 💳 Metode Pembayaran

* GoPay, OVO, DANA, ShopeePay
* QRIS (dengan biaya layanan 0,7%)
* Transfer Bank BCA & Mandiri
* Kartu Kredit/Debit (dengan biaya layanan 2,9%)
* Pulsa Telkomsel & XL/Axis (dengan biaya layanan 5%)
* Kalkulasi total otomatis berdasarkan metode bayar yang dipilih

### 🛒 Pemesanan & Checkout

* Panel order interaktif di halaman top up
* Halaman konfirmasi pesanan sebelum pembayaran
* Generasi kode pesanan unik (format: NXT + 9 karakter)
* Halaman sukses pesanan dengan ringkasan transaksi lengkap

### 📋 Riwayat Transaksi

* Daftar seluruh transaksi pengguna (hanya untuk user login)
* Status pesanan: Menunggu, Diproses, Selesai, Gagal
* Pagination (10 transaksi per halaman)

### 🛠️ Dashboard Admin

* Statistik total pesanan, pesanan hari ini, pesanan pending
* Statistik total pendapatan (keseluruhan & hari ini)
* Statistik total user dan produk aktif
* Tabel pesanan terbaru dengan ubah status langsung
* Pengelolaan produk: tambah produk baru, aktifkan/nonaktifkan, hapus
* Pengelolaan pesanan: filter berdasarkan pencarian & status, ubah status, hapus
* Dukungan pagination pada tabel pesanan admin

## 🗺️ Sitemap

### Guest

* Home (Beranda)
* Top Up (Katalog Produk)
* Login
* Register

### User

* Home (Beranda)
* Top Up (Katalog Produk)
* Checkout (Konfirmasi Pesanan)
* Selesai (Halaman Sukses Pesanan)
* Riwayat Transaksi
* Logout

### Admin

* Dashboard Admin
* Kelola Produk (Tambah, Aktifkan/Nonaktifkan, Hapus)
* Kelola Pesanan (Filter, Ubah Status, Hapus)
* Logout

## 📁 Struktur Folder

```text
Nexa_TopupBARU/
│
├── pages/
│   ├── index.php
│   ├── topup.php
│   ├── checkout.php
│   └── history.php
│
├── admin/
│   ├── dashboard.php
│   ├── products.php
│   └── orders.php
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── config/
│   ├── db.php
│   └── session.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── assets/
│   ├── style.css
│   └── script.js
│
└── sql/
    └── database.sql
```

## 💻 Tech Stack

### Frontend

* HTML5
* CSS3
* JavaScript (Vanilla)

### Backend

* PHP Native

### Database

* MySQL

### Development Tools

* XAMPP
* phpMyAdmin
* Visual Studio Code
* Google Chrome

## ⚙️ Cara Instalasi

1. Clone atau ekstrak project ke folder `htdocs` pada XAMPP.
2. Buka phpMyAdmin dan buat database baru bernama `nexa_topup`.
3. Import file `sql/database.sql` ke database tersebut.
4. Sesuaikan konfigurasi koneksi database pada `config/db.php` jika diperlukan.
5. Jalankan XAMPP (Apache + MySQL) dan akses `http://localhost/Nexa_TopupBARU/pages/index.php`.
6. Login sebagai admin menggunakan:
   * **Email:** `admin@nexatopup.com`
   * **Password:** `password`

## 🗄️ Struktur Database

| Tabel             | Keterangan                                         |
| ----------------- | -------------------------------------------------- |
| `users`           | Data pengguna dan admin                            |
| `categories`      | Kategori produk (Diamond, Membership, dll.)        |
| `products`        | Data produk top up beserta harga dan badge         |
| `orders`          | Data pesanan transaksi pengguna                    |
| `payment_methods` | Metode pembayaran beserta kode biaya layanan       |

# AI Usage Statement

## 1) Tool

* ChatGPT (OpenAI)
* Claude (Anthropic)

## 2) Untuk apa

Membantu proses pengembangan website Nexa_Topup berbasis PHP, MySQL, HTML, CSS, dan JavaScript, mulai dari pembuatan struktur kode, implementasi fitur pemesanan dan checkout, pengelolaan session dan autentikasi, pengembangan panel admin, hingga debugging error dan perbaikan antarmuka (UI/UX).

## 3) 2–3 prompt utama

* "Buatkan sistem top up game berbasis PHP Native dengan fitur katalog produk, pemesanan, checkout, dan riwayat transaksi."
* "Bantu kembangkan panel admin untuk mengelola produk dan pesanan dengan fitur filter, ubah status, dan statistik dashboard."
* "Perbaiki alur checkout agar validasi User ID, Zone ID, dan metode pembayaran berjalan dengan benar sebelum pesanan disimpan."

## 4) Bagian output AI yang dipakai

* Referensi kode PHP untuk halaman login, register, top up, checkout, dan riwayat transaksi.
* Referensi kode PHP untuk panel admin beserta fitur CRUD produk dan manajemen status pesanan.
* Referensi kode HTML, CSS, dan JavaScript untuk tampilan katalog produk interaktif, countdown flash sale, dan panel order dinamis.
* Referensi query MySQL dan struktur database yang digunakan pada seluruh fitur website.

## 5) Bagian yang saya ubah + alasan

* Menyesuaikan kode AI dengan struktur folder project Nexa_Topup yang telah ditentukan sebelumnya.
* Mengubah skema query database agar tidak menggunakan JOIN berlebihan, sesuai kebutuhan performa halaman admin.
* Menyatukan penggunana java script hanya di satu file dan file .php lainnya menngunakan javascript tersebut untuk di panggil
* Mengganti penggunaan CSS inline menjadi External CSS
