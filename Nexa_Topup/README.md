# ML Shop — Top Up Mobile Legends

Platform top up Mobile Legends berbasis PHP + MySQL dengan session, database terstruktur, dan admin panel.

---

## 📁 Struktur File

```
ml-shop/
├── config/
│   ├── db.php              ← Koneksi PDO ke MySQL
│   └── session.php         ← Manajemen session + helper functions
│
├── auth/
│   ├── login.php           ← Halaman login
│   ├── register.php        ← Halaman registrasi
│   └── logout.php          ← Proses logout
│
├── pages/
│   ├── index.php           ← Halaman utama / landing
│   ├── topup.php           ← Katalog produk & form order
│   ├── checkout.php        ← Konfirmasi & selesaikan order
│   └── history.php         ← Riwayat transaksi (perlu login)
│
├── admin/
│   ├── dashboard.php       ← Dashboard statistik admin
│   ├── products.php        ← Kelola produk (CRUD)
│   └── orders.php          ← Kelola & update status pesanan
│
├── includes/
│   ├── header.php          ← HTML head + opening body
│   ├── navbar.php          ← Navigation bar
│   └── footer.php          ← Footer + closing tags
│
├── assets/
│   ├── style.css           ← Semua CSS (dark gaming theme)
│   └── script.js           ← Timer, interaksi JS
│
└── sql/
    └── database.sql        ← Schema + seed data lengkap
```

---

## 🚀 Cara Setup

### 1. Persyaratan
- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Web server: Apache (XAMPP/Laragon) atau Nginx

### 2. Import Database
```sql
-- Di phpMyAdmin atau MySQL CLI:
source /path/to/ml-shop/sql/database.sql
```

### 3. Konfigurasi Database
Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // username MySQL kamu
define('DB_PASS', '');           // password MySQL kamu
define('DB_NAME', 'mlshop');
```

### 4. Letakkan di Web Root
- XAMPP: `C:/xampp/htdocs/ml-shop/`
- Laragon: `C:/laragon/www/ml-shop/`

### 5. Akses di Browser
```
http://localhost/ml-shop/pages/index.php
```

---

## 👤 Akun Default

| Role  | Username | Email              | Password  |
|-------|----------|--------------------|-----------|
| Admin | admin    | admin@mlshop.com   | admin123  |

> **Penting:** Ganti password admin setelah setup!

---

## 🛒 Fitur Lengkap

### Untuk User
- ✅ Registrasi & Login (session PHP)
- 💎 Top Up Diamond (15 paket, mulai Rp 2.000)
- 👑 Membership (Starlight, Twilight Pass, 3 bulan)
- 🎁 Bundle Pack (Starter, Elite, Lucky)
- 📅 Weekly Diamond Pass
- 🎨 Skin Pass / Chest
- 🔍 Cek nickname akun ML (simulasi)
- 💳 9+ metode pembayaran
- 📋 Riwayat transaksi dengan pagination
- 🧾 Halaman konfirmasi & kode order

### Untuk Admin
- 📊 Dashboard statistik (orders, revenue, users, pending)
- 📦 Kelola produk: tambah, aktif/nonaktif, hapus
- 📋 Kelola pesanan: update status, filter, search, hapus
- 🔐 Proteksi halaman admin via session role

---

## 🗄️ Tabel Database

| Tabel             | Fungsi                          |
|-------------------|---------------------------------|
| `users`           | Data user & admin               |
| `categories`      | Kategori produk                 |
| `products`        | Semua produk top up             |
| `orders`          | Transaksi / pesanan             |
| `payment_methods` | Metode pembayaran               |

---

## 🔧 Pengembangan Lanjutan

- Integrasi **payment gateway** (Midtrans, Xendit, Duitku)
- API verifikasi nickname ML dari **Moonton / third party**
- **Email notifikasi** via PHPMailer
- Fitur **kupon / voucher diskon**
- **Affiliate / referral** system
- Top up via **API reseller** (Digiflazz, VCGamers, dll)
