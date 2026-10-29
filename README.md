# 🍲 Yummy Yamy Web — Project Muara
### Sistem Pemesanan Kuliner Seblak & Reservasi Meja Online Berbasis Web

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/)
[![Repository](https://img.shields.io/badge/GitHub-Project_Muara-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/WilyRadisya6/Project_Muara.git)

---

## 📌 Tentang Proyek

**Yummy Yamy Web (Project Muara)** adalah aplikasi web restoran kuliner modern yang dirancang khusus untuk mempermudah operasional pemesanan hidangan khas seblak, aneka minuman, dan sistem reservasi meja makan. 

Website ini menyediakan pengalaman pemesanan yang fleksibel dan interaktif bagi pelanggan (*Customer Facing*), mulai dari peracikan kustomisasi seblak (pilihan topping, kuah, dan level pedas), multi-metode pembayaran, hingga pembuatan faktur digital dengan QR Code. Dilengkapi pula dengan **Admin Panel terintegrasi** yang mencakup scanner QR Code, manajemen stok produk real-time, pemantauan status pesanan, serta pengelolaan reservasi meja.

---

## ✨ Fitur-Fitur Utama

### 👤 1. Halaman Pelanggan (User / Customer)
- **Beranda Interaktif (`index.php`)**: Hero section menarik, showcase hidangan seblak, serta navigasi cepat ke pemesanan dan reservasi.
- **Custom Seblak Builder (`menu.php`)**:
  - Pilihan porsi seblak yang dapat disesuaikan per individu.
  - Pilihan level kepedasan & jenis kuah sesuai selera.
  - Pilihan aneka topping (kerupuk, mie, telur, bakso, ceker, jamur enoki, cuanki, sosis, dll.) dengan harga dan ketersediaan stok *real-time*.
  - Pemesanan aneka minuman segar.
- **Keranjang Belanja Cerdas (`save_cart.php`, `get_cart.php`)**:
  - Penyimpanan keranjang berbasis session & AJAX.
  - Kalkulasi subtotal otomatis per porsi dan total belanja secara langsung.
- **Checkout & Multi-Metode Pembayaran (`checkout.php`)**:
  - Input informasi kontak pemesan (nama, telepon, email).
  - Pilihan metode pembayaran: **Tunai (Cash)**, **QRIS**, **Transfer Bank**, dan **E-Wallet** (GoPay, OVO, DANA).
  - Pembuatan kode pesanan otomatis dan unik (format: `YY<Tanggal><ID>`).
- **Konfirmasi Pesanan & Faktur Digital (`order_confirmation.php`)**:
  - Rincian ringkasan struk pesanan secara transparan.
  - **Dynamic QR Code Generator**: Memunculkan QR Code unik untuk verifikasi cepat saat pesanan diambil atau dibayar di kasir.
- **Reservasi Meja Online (`book_table.php`)**:
  - Formulir pemesanan meja makan di tempat.
  - Pemilihan tanggal, jam kunjungan, jumlah orang/kursi, dan catatan khusus (*special requests*).
- **Profil & Kontak Restoran (`about.php`, `contact.php`)**:
  - Informasi profil usaha, lokasi, jam operasional, dan form pesan pelanggan.

---

### 🛡️ 2. Panel Pengelola (Admin Panel)
- **Otentikasi Aman (`admin_panel/login.php`)**: Sistem login khusus administrator dengan password hashing terenkripsi.
- **Dashboard Statistik (`admin_panel/dashboard.php`)**:
  - Ringkasan metrik real-time: total produk menu, stok topping menipis (*low stock alert*), total reservasi pending & confirmed, serta data pesanan.
- **Manajemen Menu & Topping (`admin_panel/manage_menu.php`, `manage_toppings.php`)**:
  - Operasi CRUD (Create, Read, Update, Delete) untuk topping seblak dan menu minuman.
  - Unggah gambar produk makanan/minuman secara langsung.
  - Atur harga, kategori, ketersediaan stok, dan satuan unit (`pcs`, `porsi`, dll.).
  - Perubahan di admin langsung terintegrasi secara *real-time* ke halaman menu pelanggan.
- **Manajemen & Rincian Pesanan (`admin_panel/order_detail.php`)**:
  - Menampilkan rincian detail pesanan pelanggan per porsi dan minuman.
  - Update status pesanan (*Pending*, *Processing*, *Ready*, *Completed*, *Cancelled*).
  - Update status pembayaran (*Pending*, *Paid*, *Cancelled*).
- **Scanner QR Code Kasir (`admin_panel/scan_qr.php`)**:
  - Memindai QR Code pesanan pelanggan langsung dari kamera perangkat atau input data QR untuk pencarian dan pemrosesan instan di meja kasir.
- **Manajemen Reservasi Meja (`admin_panel/bookings.php`)**:
  - Meninjau, mengonfirmasi, atau membatalkan reservasi meja tamu yang masuk.
- **Manajemen User & Akun (`admin_panel/verify_user.php`, `deactivate_user.php`, `reset_password.php`)**:
  - Kontrol aktivasi dan reset kredensial pengguna/admin.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi / Library |
|---|---|
| **Bahasa Pemrograman** | PHP (v7.4 - v8.x) |
| **Basis Data** | MySQL / MariaDB (Driver MySQLi & Prepared Statements) |
| **Frontend Styling** | HTML5, Modern CSS3 (Flexbox & CSS Grid, Custom Fonts: *Playfair Display* & *Inter*) |
| **Interaktivitas** | Vanilla JavaScript, AJAX Fetch API |
| **Fitur QR Code** | QR Code API Generator & HTML5 QR Scanner |
| **Web Server Lokal** | Apache (XAMPP / Laragon) |

---

## 📁 Struktur Direktori Proyek

```plaintext
yummy_yamy_web/
├── admin_panel/               # Modul & halaman khusus Administrator
│   ├── bookings.php           # Manajemen reservasi meja makan
│   ├── dashboard.php          # Dashboard statistik admin
│   ├── login.php              # Halaman login admin
│   ├── logout.php             # Logout session admin
│   ├── manage_menu.php        # Kelola menu minuman & topping
│   ├── manage_toppings.php    # Kelola topping seblak terperinci
│   ├── order_detail.php       # Detail rincian pesanan & ganti status
│   ├── reset_password.php     # Reset password akun
│   ├── scan_qr.php            # Fitur pemindai QR Code kasir
│   └── ...                    # File pendukung admin
├── assets/                    # File statis (Gambar, CSS tambahan, JS)
│   ├── js/
│   │   └── site.js            # Script logika frontend
│   ├── menu_items/            # Direktori upload foto produk menu
│   └── *.jpg, *.png           # Aset visual & logo restoran
├── includes/                  # Komponen reusable template & modul inti
│   ├── config.php             # Konfigurasi dasar web
│   ├── database.php           # Class Admin & koneksi DB
│   ├── header.php             # Komponen header umum
│   └── footer.php             # Komponen footer umum
├── about.php                  # Halaman profil restoran
├── book_table.php             # Halaman formulir booking meja
├── checkout.php               # Halaman checkout & pilihan pembayaran
├── contact.php                # Halaman kontak restoran
├── db_connect.php             # Konfigurasi koneksi MySQLi utama
├── index.php                  # Halaman utama / landing page
├── menu.php                   # Halaman pemesanan kustom seblak & minuman
├── order_confirmation.php     # Halaman konfirmasi order & QR Code
├── setup_database.php         # Script setup awal database & tabel menu/topping
├── setup_orders_table.php     # Script setup awal tabel pesanan (orders)
├── migrate_add_unit.php       # Migrasi penambahan kolom unit produk
├── create_simple_admin.php    # Script pembuat/reset akun admin default
├── style.css                  # Stylesheet utama website
└── README.md                  # Dokumentasi proyek
```

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal menggunakan **XAMPP** atau **Laragon**:

### 1. Kloning Repository
Buka terminal / Git Bash di folder `htdocs` (atau folder `www` jika menggunakan Laragon):

```bash
cd C:/xampp/htdocs
git clone https://github.com/WilyRadisya6/Project_Muara.git yummy_yamy_web
cd yummy_yamy_web
```

### 2. Nyalakan Web Server & Database
1. Buka **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **Apache** dan **MySQL**.

### 3. Konfigurasi Database
Buka file `db_connect.php` dan `includes/database.php` untuk memastikan kredensial database sudah sesuai:
```php
$servername = "localhost";
$username   = "root";
$password   = "";             // Kosongkan jika default XAMPP/Laragon
$dbname     = "yummy_yamy_db"; // Nama database
```

### 4. Inisialisasi Database Otomatis
Buka browser dan jalankan script inisialisasi tabel berikut secara berurutan:
1. **Membuat Database & Tabel Menu/Topping**:
   `http://localhost/yummy_yamy_web/setup_database.php`
2. **Membuat Tabel Pesanan & Minuman**:
   `http://localhost/yummy_yamy_web/setup_orders_table.php`
3. **Migrasi Kolom Satuan (Unit)**:
   `http://localhost/yummy_yamy_web/migrate_add_unit.php`
4. **Membuat Akun Administrator Default**:
   `http://localhost/yummy_yamy_web/create_simple_admin.php`

### 5. Kredensial Administrator Default
Setelah menjalankan `create_simple_admin.php`, akun administrator default adalah:
- **URL Admin Panel**: `http://localhost/yummy_yamy_web/admin_panel/`
- **Email**: `admin@yummyyamy.com`
- **Password**: `admin123`

---

## 🌐 Akses Aplikasi

| Halaman | URL Akses Lokal |
|---|---|
| **Beranda / User Page** | `http://localhost/yummy_yamy_web/index.php` |
| **Pemesanan Menu Seblak** | `http://localhost/yummy_yamy_web/menu.php` |
| **Reservasi Meja** | `http://localhost/yummy_yamy_web/book_table.php` |
| **Login Admin Panel** | `http://localhost/yummy_yamy_web/admin_panel/login.php` |
| **Dashboard Admin** | `http://localhost/yummy_yamy_web/admin_panel/dashboard.php` |
| **Scanner QR Kasir** | `http://localhost/yummy_yamy_web/admin_panel/scan_qr.php` |

---

## 📤 Panduan Push ke GitHub

Jika Anda ingin mengunggah (push) perubahan atau kode proyek ini ke repository GitHub:

```bash
# Masuk ke folder proyek
cd C:/xampp/htdocs/yummy_yamy_web

# Inisialisasi git (jika belum pernah diinisialisasi)
git init

# Tambahkan remote repository
git remote add origin https://github.com/WilyRadisya6/Project_Muara.git

# Pastikan berada di branch main
git branch -M main

# Tambahkan seluruh file ke staging area
git add .

# Buat commit pertama
git commit -m "feat: initial commit project muara yummy yamy web with complete features"

# Push ke repository GitHub
git push -u origin main
```

*(Catatan: Jika repository remote di GitHub sudah memiliki commit/file awal seperti commit default, Anda dapat menambahkan `--force` atau melakukan `git pull origin main --allow-unrelated-histories` sebelum push).*

---

## 👥 Pengembang

- **Wily Radisya** — [*@WilyRadisya6*](https://github.com/WilyRadisya6)
- **Repository**: [https://github.com/WilyRadisya6/Project_Muara.git](https://github.com/WilyRadisya6/Project_Muara.git)

---

## 📄 Lisensi
Proyek ini dikembangkan untuk tujuan edukasi dan pengembangan sistem informasi kuliner. Bebas dikembangkan lebih lanjut dengan mencantumkan atribusi kepada pengembang.

#   P r o j e c t _ M u a r a  
 