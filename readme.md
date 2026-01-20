# SISTEM PEMESANAN BARANG

Sistem manajemen pemesanan barang berbasis web dengan PHP, MySQL, dan Bootstrap.

## 🚀 FITUR UTAMA

### 🔐 Autentikasi & Keamanan
- Multi-level user (Admin, Manager, Staff)
- Secure login dengan password hashing
- Session management
- Auto logout setelah idle

### 📦 Manajemen Produk
- CRUD produk lengkap
- Kategori produk
- Manajemen stok
- Upload gambar produk

### 👥 Manajemen Pelanggan
- Data pelanggan lengkap
- Riwayat pesanan
- Kode pelanggan unik
- Status aktif/nonaktif

### 🛒 Manajemen Pesanan
- Buat, edit, hapus pesanan
- Multiple items per order
- Status pesanan (Pending, Processing, Shipped, Delivered, Cancelled)
- Status pembayaran (Unpaid, Partial, Paid)
- Invoice/print system

### 📊 Dashboard & Laporan
- Statistik real-time
- Laporan penjualan
- Export data (CSV, PDF)
- Filter dan pencarian

## 📋 PERSYARATAN SISTEM

- PHP 7.4 atau lebih baru
- MySQL 5.7 / MariaDB 10.2+
- Apache/Nginx web server
- Extensions: PDO, MySQLi, GD (untuk gambar)

## 🛠 INSTALASI

### 1. Upload Files
Upload semua file ke server web Anda di folder `public_html` atau `www`.

### 2. Buat Database
1. Login ke phpMyAdmin
2. Buat database baru: `sistem_pemesanan_barang`
3. Import file `database.sql`

### 3. Konfigurasi
Edit file `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nama_database_anda');
define('DB_USER', 'username_database');
define('DB_PASS', 'password_database');
define('BASE_URL', 'https://domain-anda.com/');