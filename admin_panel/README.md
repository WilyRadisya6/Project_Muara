# Admin Panel - Yummy Yamy

Panel admin terpisah untuk mengelola website Yummy Yamy.

## Akses Admin Panel

URL: `http://localhost/yummy_yamy_web/admin_panel/`

Atau langsung ke halaman login: `http://localhost/yummy_yamy_web/admin_panel/login.php`

## Fitur

1. **Dashboard** - Overview statistik users dan toppings
2. **Manage Menu** - Tambah, edit, dan update topping, minuman, dan item menu lainnya
3. **Manage Users** - Verifikasi, deactivate, reset password, dan hapus user

## File Structure

```
admin_panel/
├── index.php              # Redirect ke login
├── login.php              # Halaman login admin
├── dashboard.php          # Dashboard admin
├── manage_menu.php        # Kelola menu (topping + minuman)
├── verify_user.php        # Verifikasi user
├── deactivate_user.php    # Deactivate user
├── delete_user.php        # Hapus user
├── reset_password.php     # Reset password user
└── logout.php             # Logout admin
```

## Default Admin Credentials

Email: `admin@yummyyamy.com`
Password: `admin123`

*Gunakan `create_simple_admin.php` di root untuk membuat/update admin account.*

## Catatan

- Admin panel terpisah dari website user
- Tidak ada link ke admin panel dari website user
- Akses admin panel hanya melalui URL langsung

