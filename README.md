# Nexa_TopUp

# NexaTopup: Sistem Transaksi Top Up Mobile Legends

NexaTopup adalah website top up Mobile Legends berbasis PHP dan MySQL yang menyediakan layanan pembelian diamond, membership, dan bundle item secara online. Sistem ini dilengkapi dengan fitur autentikasi pengguna, riwayat transaksi, dashboard admin, serta berbagai metode pembayaran digital.

## ✨ Fitur Utama

### 👤 User
- Login & Register menggunakan session PHP
- Top up diamond Mobile Legends
- Pembelian membership:
  - Starlight Member
  - Starlight Plus
  - Twilight Pass
- Pembelian special bundle & pack
- Pilihan metode pembayaran:
  - GoPay
  - OVO
  - DANA
  - ShopeePay
  - QRIS
  - Transfer Bank
  - Kartu Kredit
  - Pulsa
- Riwayat transaksi pengguna
- Tampilan responsive dan modern

### ⚙️ Admin
- Dashboard admin
- Melihat statistik transaksi
- CRUD produk top up
- Mengelola pesanan
- Update status transaksi

---

# 🗂 Struktur Project

```bash
/ml-shop/
├── config/
│   ├── db.php
│   └── session.php
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
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

🛠 Teknologi yang Digunakan
PHP Native
MySQL
HTML5
CSS
JavaScript
Session PHP
XAMPP / Apache
