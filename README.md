# ⚡ NexaTopup — Sistem Transaksi Top Up Mobile Legends

**NexaTopup** — Sistem Transaksi Top Up Mobile Legends berbasis web menggunakan PHP Native dan MySQL.

---

## 📖 Description

**NexaTopup** adalah website layanan top up game Mobile Legends yang memungkinkan pengguna melakukan pembelian diamond, membership, dan bundle item secara online dengan berbagai metode pembayaran digital.

Sistem menyediakan fitur autentikasi pengguna, riwayat transaksi, dashboard admin, serta manajemen produk dan pesanan. Website dirancang menggunakan PHP Native, MySQL, HTML, CSS, dan JavaScript dengan tampilan modern dan responsive.

---

## 👥 Team Members & Responsibilities

### 1. 🎨 Lalu Farras Hanif Aslam — Frontend Developer
`F1D02410118`

#### Responsibilities
- Mendesain tampilan website
- Membuat halaman UI/UX
- Mengatur responsive design
- Membuat interaksi menggunakan JavaScript
- Styling menggunakan CSS

---

### 2. ⚙️ Muhammad Fathan Abdullah — Backend Developer
`F1D02410124`

#### Responsibilities
- Membuat logic sistem menggunakan PHP
- Membuat autentikasi login/register
- Mengelola session user
- Membuat proses checkout
- Membuat CRUD admin
- Menghubungkan website ke database

---

### 3. 🗄️ Muhammad Ikbal — Database Designer / System Analyst
`F1D02410141`

#### Responsibilities
- Mendesain database
- Membuat relasi tabel
- Membuat query SQL
- Mengelola struktur data transaksi
- Dokumentasi project
- Testing sistem

---

## 👤 User / Actor Website

### 👥 User
- Register akun
- Login akun
- Top up Diamond Mobile Legends
- Membeli membership
- Melihat riwayat transaksi
- Logout akun

### ⚙️ Admin
- Login admin
- Mengelola produk top up
- Mengelola transaksi
- Update status pesanan
- Melihat statistik transaksi
- Menghapus / mengedit produk

---

## ✨ Main Features

### 👤 User Features
- Login & Register menggunakan PHP Session
- Top up Diamond Mobile Legends
- Membership purchase:
  - Starlight Member
  - Starlight Plus
  - Twilight Pass
- Special bundle purchase
- Responsive UI
- Riwayat transaksi
- Multiple payment methods:
  - GoPay
  - OVO
  - DANA
  - ShopeePay
  - QRIS
  - Transfer Bank
  - Kartu Kredit
  - Pulsa

### ⚙️ Admin Features
- Dashboard Admin
- CRUD Product
- Manage Orders
- Update Transaction Status
- Transaction Statistics

---

## 🗃️ Database Management System (DBMS)

**DBMS Used:** MySQL + phpMyAdmin

---

## 📂 Project Structure

```bash
Nexa_Topup/
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
```

---

## 🌐 Alamat Website

```txt
http://localhost/Nexa_Topup/
```
