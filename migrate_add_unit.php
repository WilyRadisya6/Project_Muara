<?php
/**
 * Database Migration - Add Unit Column
 * Tambahkan kolom 'unit' ke tabel toppings dan menu
 */

include 'db_connect.php';

$results = [
    'toppings_migration' => false,
    'menu_migration' => false,
    'errors' => []
];

// Check if column exists before adding
$checkToppings = "SHOW COLUMNS FROM toppings LIKE 'unit'";
$result = $conn->query($checkToppings);

if ($result && $result->num_rows == 0) {
    // Column doesn't exist, add it
    $alterToppings = "ALTER TABLE toppings ADD COLUMN unit VARCHAR(20) NOT NULL DEFAULT 'pcs' AFTER stock";
    if ($conn->query($alterToppings)) {
        $results['toppings_migration'] = true;
    } else {
        $results['errors'][] = 'Toppings migration failed: ' . $conn->error;
    }
} else {
    $results['toppings_migration'] = true; // Already exists
}

// Check menu table
$checkMenu = "SHOW COLUMNS FROM menu LIKE 'unit'";
$result = $conn->query($checkMenu);

if ($result && $result->num_rows == 0) {
    // Column doesn't exist, add it
    $alterMenu = "ALTER TABLE menu ADD COLUMN unit VARCHAR(20) NOT NULL DEFAULT 'pcs' AFTER stock";
    if ($conn->query($alterMenu)) {
        $results['menu_migration'] = true;
    } else {
        $results['errors'][] = 'Menu migration failed: ' . $conn->error;
    }
} else {
    $results['menu_migration'] = true; // Already exists
}

if ($conn) $conn->close();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration - Add Unit Column</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 20px; text-align: center; }
        .status { padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        .status.success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .status.error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        .button { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; text-align: center; }
        .button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Migration</h1>
        
        <?php if ($results['toppings_migration'] && $results['menu_migration']): ?>
            <div class="status success">
                ✅ <strong>Migration Berhasil!</strong><br>
                Kolom 'unit' sudah ditambahkan ke tabel toppings dan menu.
            </div>
            <div class="info">
                Sekarang Anda bisa kembali ke Manage Menu dan mulai menggunakan fitur satuan stok.
            </div>
            <a href="admin_panel/manage_menu.php" class="button">➜ Buka Manage Menu</a>
        <?php else: ?>
            <div class="status error">
                ❌ <strong>Migration Gagal!</strong><br>
                <?php if (!empty($results['errors'])): ?>
                    <?php foreach ($results['errors'] as $error): ?>
                        <div>• <?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="info">
                Jika kolom sudah ada, Anda bisa langsung lanjut menggunakan Manage Menu.
            </div>
            <a href="admin_panel/manage_menu.php" class="button">➜ Kembali ke Manage Menu</a>
        <?php endif; ?>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
        
        <div style="font-size: 12px; color: #666; margin-top: 20px;">
            <p><strong>Informasi:</strong></p>
            <p>File ini hanya perlu dijalankan sekali untuk menambahkan kolom 'unit' ke database.</p>
            <p>Jika Anda melihat halaman ini lagi, berarti kolom sudah ada atau migration berhasil.</p>
        </div>
    </div>
</body>
</html>
