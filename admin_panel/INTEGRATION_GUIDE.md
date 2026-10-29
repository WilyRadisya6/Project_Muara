# Panduan Integrasi Menu Admin dengan User Menu

## 📋 Deskripsi Integrasi

Sistem admin panel **Manage Menu** terintegrasi penuh dengan halaman **menu.php** user. Perubahan yang dilakukan di admin panel akan langsung tercermin di halaman user tanpa perlu penambahan kode atau konfigurasi tambahan.

---

## 🗄️ Struktur Database

### Tabel: `toppings`
Menyimpan data topping untuk seblak
```
- id (INT, Primary Key)
- name (VARCHAR 100) - Nama topping
- description (TEXT) - Deskripsi (opsional)
- price (DECIMAL 10,2) - Harga topping
- stock (INT) - Stok tersedia
- image (VARCHAR 255) - Path gambar topping
- created_at, updated_at (TIMESTAMP)
```

### Tabel: `menu`
Menyimpan data minuman dan item lain
```
- id (INT, Primary Key)
- name (VARCHAR 150) - Nama item
- description (TEXT) - Deskripsi
- price (DECIMAL 10,2) - Harga
- category (VARCHAR 50) - Kategori (drink, other, dll)
- stock (INT) - Stok tersedia
- image (VARCHAR 255) - Path gambar
- created_at, updated_at (TIMESTAMP)
```

---

## 🔄 Alur Integrasi Data

### Dari Admin ke User (Read Flow)

```
1. Admin Panel (manage_menu.php)
   ├─ Tambah/Edit/Delete Topping → toppings table
   ├─ Tambah/Edit/Delete Minuman → menu table (category='drink')
   └─ Update Stok → toppings/menu table

2. User Page (menu.php)
   ├─ Query: SELECT * FROM toppings ORDER BY id
   ├─ Query: SELECT * FROM menu WHERE category='drink' ORDER BY id
   └─ Display: Tampilkan data real-time di halaman user

3. User Interaction
   ├─ Select Topping → Add to Cart
   ├─ Select Drink → Add to Cart
   └─ Checkout → Save to session
```

### Data Flow Diagram

```
┌─────────────────────────────────┐
│   ADMIN PANEL MANAGE MENU        │
│  ┌──────────────────────────────┐│
│  │ Tab 1: Manage Toppings       ││
│  │ - Add/Edit/Delete/Update     ││
│  │ - Manage stok                ││
│  └──────────────────────────────┘│
│  ┌──────────────────────────────┐│
│  │ Tab 2: Manage Minuman        ││
│  │ - Add/Edit/Delete/Update     ││
│  │ - Manage stok                ││
│  └──────────────────────────────┘│
└──────────────┬──────────────────┘
               │ CRUD Operations
               ▼
        ┌─────────────────┐
        │  DATABASE       │
        │ ┌─────────────┐ │
        │ │ toppings    │ │
        │ │ table       │ │
        │ └─────────────┘ │
        │ ┌─────────────┐ │
        │ │ menu table  │ │
        │ │ (drinks)    │ │
        │ └─────────────┘ │
        └─────────────────┘
               │ Query
               ▼
┌─────────────────────────────────┐
│   USER PAGE MENU                 │
│  ┌──────────────────────────────┐│
│  │ Section 1: Pilihan Topping   ││
│  │ - Load dari toppings table   ││
│  │ - Display with checkbox      ││
│  │ - Real-time stok            ││
│  └──────────────────────────────┘│
│  ┌──────────────────────────────┐│
│  │ Section 2: Menu Minuman      ││
│  │ - Load dari menu table       ││
│  │ - Display cards              ││
│  │ - Real-time stok            ││
│  └──────────────────────────────┘│
└─────────────────────────────────┘
```

---

## 📊 Feature Integration Matrix

| Feature | Admin Panel | User Page | Status |
|---------|-----------|-----------|--------|
| Add Topping | ✅ Tab Toppings | - | ✅ |
| Edit Topping | ❌ (dapat ditambahkan) | - | 🔄 |
| Delete Topping | ✅ Tab Toppings | - | ✅ |
| Update Stock Topping | ✅ Tab Toppings | Real-time | ✅ |
| Add Drink | ✅ Tab Minuman | - | ✅ |
| Edit Drink | ❌ (dapat ditambahkan) | - | 🔄 |
| Delete Drink | ✅ Tab Minuman | - | ✅ |
| Update Stock Drink | ✅ Tab Minuman | Real-time | ✅ |
| Display Toppings | - | ✅ Grid display | ✅ |
| Display Drinks | - | ✅ Grid display | ✅ |
| Add to Cart | - | ✅ | ✅ |
| Checkout | - | ✅ | ✅ |

**Legenda:** ✅ = Sudah implemented | ❌ = Belum implemented | 🔄 = Optional/Future

---

## 🔗 Integration Points

### 1. Admin Panel Entry Point
**File:** `admin_panel/manage_menu.php`

```php
// Connection: include '../db_connect.php'
// Tables: toppings, menu

// Create Tables (auto-create if not exists)
$create_toppings_table = "CREATE TABLE IF NOT EXISTS toppings ...";
$create_menu_table = "CREATE TABLE IF NOT EXISTS menu ...";

// CRUD Operations
- Add item: INSERT INTO toppings/menu
- Update stock: UPDATE toppings/menu SET stock
- Delete item: DELETE FROM toppings/menu
- List items: SELECT * FROM toppings/menu
```

### 2. User Page Entry Point
**File:** `menu.php`

```php
// Connection: include 'db_connect.php'
// Display Tables: toppings, menu

// Query Toppings
$result = $conn->query("SELECT * FROM toppings ORDER BY id");

// Query Drinks
$sql = "SELECT * FROM menu WHERE category = 'drink' ORDER BY id";
$result = $conn->query($sql);

// Display in Grid
foreach ($toppings as $topping) { ... }
foreach ($drinks as $drink) { ... }
```

### 3. Shared Database Connection
**File:** `db_connect.php`

```php
// Single database connection used by both:
// - Admin Panel (manage_menu.php)
// - User Page (menu.php)

// Connection Details
$servername = "...";
$username = "...";
$password = "...";
$dbname = "...";
```

---

## 💡 Cara Kerja Integrasi

### Scenario 1: Admin Menambah Topping
```
1. Admin buka Manage Menu → Tab Toppings
2. Admin isi form:
   - Nama: "Perkedel"
   - Harga: Rp 4.000
   - Stok: 50
   - Gambar: (upload)
3. Klik "Tambah Topping"
4. Data INSERT ke tabel `toppings`
5. User buka menu.php
6. Data "Perkedel" langsung tampil di Pilihan Topping
```

### Scenario 2: Admin Update Stok Topping
```
1. Admin buka Manage Menu → Tab Toppings
2. Admin lihat tabel Daftar Topping
3. Admin ubah stok "Telur" dari 50 → 30
4. Klik Update
5. Data UPDATE di tabel `toppings` (stock = 30)
6. User lihat menu.php
7. Stok "Telur" langsung update menjadi 30
8. User tidak bisa select lebih dari 30
```

### Scenario 3: Admin Menambah Minuman
```
1. Admin buka Manage Menu → Tab Minuman
2. Admin isi form:
   - Nama: "Jus Mangga"
   - Harga: Rp 12.000
   - Stok: 40
   - Kategori: drink
   - Gambar: (upload)
3. Klik "Tambah Minuman"
4. Data INSERT ke tabel `menu` (category='drink')
5. User buka menu.php
6. Data "Jus Mangga" langsung tampil di Menu Minuman
```

---

## 🔍 Query Reference

### Admin Panel Queries

#### Get All Toppings
```sql
SELECT * FROM toppings ORDER BY id DESC
```

#### Get All Drinks
```sql
SELECT * FROM menu ORDER BY id DESC
```

#### Update Stock (Topping)
```sql
UPDATE toppings SET stock = ? WHERE id = ?
```

#### Update Stock (Drink)
```sql
UPDATE menu SET stock = ? WHERE id = ?
```

#### Delete Topping
```sql
DELETE FROM toppings WHERE id = ?
```

#### Delete Drink
```sql
DELETE FROM menu WHERE id = ?
```

### User Page Queries

#### Get All Toppings (Display)
```sql
SELECT * FROM toppings ORDER BY id
```

#### Get All Drinks (Display)
```sql
SELECT * FROM menu WHERE category = 'drink' ORDER BY id
```

---

## 📱 Real-time Updates

**Catatan:** Integrasi saat ini menggunakan **server-side refresh** (user perlu reload halaman untuk melihat update terbaru).

### Untuk real-time updates tanpa refresh (optional):
Dapat ditambahkan:
1. **AJAX polling** - Query database setiap X detik
2. **WebSocket** - Real-time push dari server
3. **Service Worker** - Background sync

---

## 🛠️ Troubleshooting

### Problem 1: Data tidak muncul di user page
**Solusi:**
```
1. Cek koneksi database di db_connect.php
2. Cek query di menu.php berjalan dengan benar
3. Refresh halaman (F5)
4. Check browser console untuk error
```

### Problem 2: Stok tidak update di user page
**Solusi:**
```
1. Cek UPDATE query di manage_menu.php berhasil
2. Refresh halaman menu.php
3. Cek nilai stok di database langsung
4. Clear browser cache
```

### Problem 3: Gambar tidak muncul
**Solusi:**
```
1. Cek path gambar di database
2. Cek folder assets/menu_items/ exist
3. Cek file permissions
4. Check browser console untuk 404 errors
```

---

## 📝 Files Involved in Integration

```
c:\laragon\www\yummy_yamy_web\
├── db_connect.php          (Shared database connection)
├── menu.php                (User page - display + order)
├── admin_panel/
│   ├── manage_menu.php     (Admin panel - CRUD operations)
│   ├── dashboard.php       (Quick stats from both tables)
│   └── README.md
├── assets/
│   ├── menu_items/         (Uploaded images folder)
│   └── (topping images)
└── includes/
    └── (header, footer templates)
```

---

## ✅ Integration Checklist

- [x] Database tables (toppings, menu) created
- [x] Admin panel CRUD operations working
- [x] User page displays data from database
- [x] Stock management integrated
- [x] Image upload/display working
- [x] Category separation (topping vs drink)
- [x] Tab navigation in admin panel
- [x] Real-time stock display
- [ ] Edit functionality in admin panel (optional)
- [ ] Real-time push updates without refresh (optional)

---

## 🚀 Next Steps

1. **Testing:** Lakukan test add/edit/delete di admin panel dan lihat perubahan di user page
2. **Performance:** Monitor database queries jika item menu bertambah banyak
3. **Enhancement:** Tambah edit functionality untuk update nama/harga (tidak hanya stok)
4. **UI/UX:** Tambah loading state, success notifications, error handling
5. **Security:** Add validation untuk uploaded images, SQL injection prevention

---

## 📞 Support

Untuk pertanyaan atau issue terkait integrasi, periksa:
1. `admin_panel/manage_menu.php` - Admin logic
2. `menu.php` - User display logic
3. `db_connect.php` - Database connection
4. Browser console - JavaScript errors
5. Server logs - PHP errors
