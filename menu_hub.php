<?php
/**
 * Menu Management Hub
 * Halaman pusat untuk manage menu admin dan akses user menu
 */
session_start();

// Simple redirect based on user type
if (isset($_GET['go'])) {
    $go = $_GET['go'];
    if ($go === 'admin') {
        header('Location: admin_panel/manage_menu.php');
        exit;
    } elseif ($go === 'menu') {
        header('Location: menu.php');
        exit;
    } elseif ($go === 'test') {
        header('Location: test_menu_integration.php');
        exit;
    }
}

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Yummy Yamy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            width: 100%;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 36px;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .card-header {
            padding: 20px;
            color: white;
            font-weight: 600;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .card-admin {
            background: linear-gradient(135deg, #FF6B35 0%, #ff5a1f 100%);
        }
        .card-user {
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
        }
        .card-test {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .card-body {
            padding: 20px;
            color: #333;
        }
        .card-body p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
            color: #666;
        }
        .features {
            list-style: none;
            margin-bottom: 16px;
        }
        .features li {
            padding: 6px 0;
            font-size: 13px;
            color: #666;
        }
        .features li:before {
            content: "✓ ";
            color: #4CAF50;
            font-weight: bold;
            margin-right: 8px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        .btn-admin {
            background: #FF6B35;
            color: white;
        }
        .btn-admin:hover {
            background: #ff5a1f;
        }
        .btn-user {
            background: #00f2fe;
            color: #0066cc;
        }
        .btn-user:hover {
            background: #4facfe;
        }
        .btn-test {
            background: #43e97b;
            color: white;
        }
        .btn-test:hover {
            background: #38f9d7;
        }
        .btn-disabled {
            background: #ccc;
            color: #999;
            cursor: not-allowed;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }
        .status-ready {
            background: #d4edda;
            color: #155724;
        }
        .status-login {
            background: #fff3cd;
            color: #856404;
        }
        .docs-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .docs-section h3 {
            color: #333;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .docs-section a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .docs-section a:hover {
            text-decoration: underline;
        }
        .docs-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }
        .docs-list li {
            padding: 12px;
            background: #f5f5f5;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .docs-list a {
            font-size: 14px;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍜 Yummy Yamy Menu</h1>
            <p>Pusat Manajemen Menu & Integrasi Admin-User</p>
        </div>

        <div class="cards-grid">
            <!-- Card Admin -->
            <div class="card">
                <div class="card-header card-admin">
                    ⚙️ Admin Menu Management
                </div>
                <div class="card-body">
                    <p>Kelola menu, tambah topping, upload gambar, dan update stok.</p>
                    <ul class="features">
                        <li>Tambah/Edit/Hapus Topping</li>
                        <li>Tambah/Edit/Hapus Minuman</li>
                        <li>Upload gambar dari file</li>
                        <li>Update stok real-time</li>
                        <li>Preview gambar saat edit</li>
                    </ul>
                    <div class="info-box">
                        <?php if ($is_admin): ?>
                            ✅ Anda sudah login sebagai admin
                        <?php else: ?>
                            ⚠️ Perlu login sebagai admin terlebih dahulu
                        <?php endif; ?>
                    </div>
                    <a href="?go=admin" class="btn btn-admin <?php echo !$is_admin ? 'btn-disabled' : ''; ?>" <?php echo !$is_admin ? 'style="cursor:not-allowed;opacity:0.6"' : ''; ?>>
                        Buka Admin Panel
                    </a>
                    <span class="status-badge status-ready">✓ Ready</span>
                </div>
            </div>

            <!-- Card User -->
            <div class="card">
                <div class="card-header card-user">
                    🍽️ User Menu Display
                </div>
                <div class="card-body">
                    <p>Lihat menu yang sudah dikelola admin dengan harga, stok, dan gambar real-time.</p>
                    <ul class="features">
                        <li>Tampil topping dengan gambar</li>
                        <li>Tampil minuman & harga</li>
                        <li>Stok otomatis update</li>
                        <li>Harga dari admin</li>
                        <li>Order & checkout</li>
                    </ul>
                    <div class="info-box">
                        ✅ Terbuka untuk semua pengguna
                    </div>
                    <a href="?go=menu" class="btn btn-user">
                        Lihat Menu
                    </a>
                    <span class="status-badge status-ready">✓ Ready</span>
                </div>
            </div>

            <!-- Card Test -->
            <div class="card">
                <div class="card-header card-test">
                    🧪 System Test & Verification
                </div>
                <div class="card-body">
                    <p>Verifikasi bahwa sistem terintegrasi dengan baik dan siap digunakan.</p>
                    <ul class="features">
                        <li>Cek database connection</li>
                        <li>Verifikasi tabel & data</li>
                        <li>Cek folder upload</li>
                        <li>Laporan lengkap</li>
                        <li>Status keseluruhan</li>
                    </ul>
                    <div class="info-box">
                        📊 Jalankan test untuk verifikasi setup
                    </div>
                    <a href="?go=test" class="btn btn-test">
                        Jalankan Test
                    </a>
                    <span class="status-badge status-ready">✓ Ready</span>
                </div>
            </div>
        </div>

        <!-- Documentation -->
        <div class="docs-section">
            <h3>📚 Dokumentasi & Panduan</h3>
            <p style="margin-bottom: 16px; color: #666;">Pelajari lebih lanjut tentang sistem integrasi menu:</p>
            <ul class="docs-list">
                <li><a href="MENU_QUICKSTART.md" target="_blank">⚡ Quick Start Guide</a></li>
                <li><a href="MENU_INTEGRATION_DOCS.md" target="_blank">📖 Dokumentasi Lengkap</a></li>
                <li><a href="MENU_READY.md" target="_blank">✅ Status & Checklist</a></li>
                <li><a href="test_menu_integration.php" target="_blank">🧪 Test Page</a></li>
            </ul>
        </div>

        <!-- Info -->
        <div class="docs-section" style="margin-top: 20px; background: #f5f5f5;">
            <h3 style="color: #666;">ℹ️ Info Sistem</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 12px;">
                <strong>Status:</strong> ✅ Production Ready
            </p>
            <p style="font-size: 13px; color: #666; margin-bottom: 12px;">
                <strong>Database:</strong> MySQL dengan prepared statements (SQL injection safe)
            </p>
            <p style="font-size: 13px; color: #666; margin-bottom: 12px;">
                <strong>Upload:</strong> File upload dengan validasi MIME type & unique filenames
            </p>
            <p style="font-size: 13px; color: #666;">
                <strong>Version:</strong> 1.0 | <strong>Last Updated:</strong> December 1, 2025
            </p>
        </div>
    </div>
</body>
</html>
