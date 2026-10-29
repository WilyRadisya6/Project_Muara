<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

$page_title = 'Manage Menu';
include '../db_connect.php';

$error = '';
$success = '';

// Buat tabel menu jika belum ada (digunakan untuk minuman dan item lain selain topping)
$create_menu_table = "CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    category VARCHAR(50) NOT NULL DEFAULT 'drink',
    stock INT NOT NULL DEFAULT 0,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    image VARCHAR(255) DEFAULT 'assets/seblak1.jpeg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Pastikan tabel toppings juga ada (dipertahankan seperti sebelumnya)
$create_toppings_table = "CREATE TABLE IF NOT EXISTS toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    image VARCHAR(255) DEFAULT 'assets/seblak1.jpeg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn && !$conn->connect_error) {
    $conn->query($create_menu_table);
    $conn->query($create_toppings_table);
}

// Fungsi untuk upload gambar
function handleImageUpload($file, $type = 'topping') {
    $upload_dir = '../assets/menu_items/';
    
    // Buat folder jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return 'assets/seblak1.jpeg'; // Default image
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null; // Error occurred
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = basename($file['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return null; // Invalid file type
    }
    
    $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    $file_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return 'assets/menu_items/' . $new_filename;
    }
    
    return null; // Upload failed
}

// Handle tambah item (topping atau drink)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_item') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $unit = trim($_POST['unit'] ?? 'pcs');
    $category = isset($_POST['category']) ? $_POST['category'] : 'drink';
    // Untuk minuman, paksa unit menjadi 'pcs' (tidak perlu pilihan satuan)
    if ($category !== 'topping') {
        $unit = 'pcs';
    }
    $image = 'assets/seblak1.jpeg'; // Default
    
    // Handle image upload untuk topping dan minuman
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploaded_image = handleImageUpload($_FILES['image'], $category);
        if ($uploaded_image !== null) {
            $image = $uploaded_image;
        } else {
            $error = 'Gagal upload gambar. Pastikan format file adalah JPG, PNG, GIF, atau WEBP.';
        }
    }

    if (empty($error)) {
        if ($category === 'topping') {
            $stmt = $conn->prepare("INSERT INTO toppings (name, price, stock, unit, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiss", $name, $price, $stock, $unit, $image);
        } else {
            $stmt = $conn->prepare("INSERT INTO menu (name, price, category, stock, unit, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdisss", $name, $price, $category, $stock, $unit, $image);
        }

        if ($stmt->execute()) {
            $success = 'Item berhasil ditambahkan ke menu.';
        } else {
            $error = 'Gagal menambahkan item: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle update stok untuk topping atau menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_stock') {
    $item_type = $_POST['item_type'];
    $item_id = intval($_POST['item_id']);
    $new_stock = intval($_POST['new_stock']);

    if ($item_type === 'topping') {
        $stmt = $conn->prepare("UPDATE toppings SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $item_id);
    } else {
        $stmt = $conn->prepare("UPDATE menu SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $item_id);
    }

    if ($stmt->execute()) {
        $success = 'Stok berhasil diupdate.';
    } else {
        $error = 'Gagal mengupdate stok: ' . $stmt->error;
    }
    $stmt->close();
}

// Handle update item (edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_item') {
    $item_id = intval($_POST['item_id']);
    $item_type = $_POST['item_type'];
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $unit = trim($_POST['unit'] ?? 'pcs');
    $image = trim($_POST['image'] ?? 'assets/seblak1.jpeg');
    
    // Handle image upload untuk topping dan minuman
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploaded_image = handleImageUpload($_FILES['image'], $item_type);
        if ($uploaded_image !== null) {
            $image = $uploaded_image;
        } else {
            $error = 'Gagal upload gambar. Pastikan format file adalah JPG, PNG, GIF, atau WEBP.';
        }
    }

    if (empty($error)) {
        if ($item_type === 'topping') {
            $stmt = $conn->prepare("UPDATE toppings SET name = ?, price = ?, stock = ?, unit = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sdissi", $name, $price, $stock, $unit, $image, $item_id);
        } else {
            $stmt = $conn->prepare("UPDATE menu SET name = ?, price = ?, stock = ?, unit = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sdissi", $name, $price, $stock, $unit, $image, $item_id);
        }

        if ($stmt->execute()) {
            $success = 'Item berhasil diupdate.';
        } else {
            $error = 'Gagal mengupdate item: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle delete item
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && isset($_GET['type'])) {
    $del_id = intval($_GET['delete']);
    $type = $_GET['type'];

    if ($type === 'topping') {
        $stmt = $conn->prepare("DELETE FROM toppings WHERE id = ?");
        $stmt->bind_param("i", $del_id);
    } else {
        $stmt = $conn->prepare("DELETE FROM menu WHERE id = ?");
        $stmt->bind_param("i", $del_id);
    }

    if ($stmt->execute()) {
        $success = 'Item berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus item: ' . $stmt->error;
    }
    $stmt->close();
}

// Ambil semua topping dan minuman/menu
$toppings = [];
$menu_items = [];
if ($conn && !$conn->connect_error) {
    $res1 = $conn->query("SELECT * FROM toppings ORDER BY id DESC");
    if ($res1) {
        while ($r = $res1->fetch_assoc()) $toppings[] = $r;
    }

    $res2 = $conn->query("SELECT * FROM menu ORDER BY id DESC");
    if ($res2) {
        while ($r = $res2->fetch_assoc()) $menu_items[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu - Yummy Yamy Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>body{background:linear-gradient(135deg,#FFF5F0 0%,#fff 100%);min-height:100vh}</style>
</head>
<body>
<!-- Navbar -->
<nav style="background:white;padding:16px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,0.06)">
    <a href="dashboard.php" style="text-decoration:none;color:inherit;display:flex;gap:12px;align-items:center"><img src="../assets/logoyummy.png" style="width:44px;height:44px;object-fit:contain"><strong>Yummy Yamy Admin</strong></a>
    <div>
        <a href="dashboard.php" style="margin-right:12px">Dashboard</a>
        <a href="manage_menu.php" style="margin-right:12px;font-weight:700;color:#FF6B35">Manage Menu</a>
        <a href="scan_qr.php" style="margin-right:12px">Scan QR</a>
        <a href="logout.php" style="background:#FF6B35;color:white;padding:8px 12px;border-radius:6px;text-decoration:none">Logout</a>
    </div>
</nav>

<section style="max-width:1200px;margin:30px auto;padding:20px">
    <h1>Manage Menu</h1>
    <p>Kelola topping dan minuman secara terpisah. Perubahan akan tercermin di halaman user `menu.php`.</p>

    <?php if ($error): ?><div style="background:#ffebee;padding:12px;border-radius:8px;margin-bottom:12px;color:#c62828"><?php echo htmlspecialchars((string)$error); ?></div><?php endif; ?>
    <?php if ($success): ?><div style="background:#e8f5e9;padding:12px;border-radius:8px;margin-bottom:12px;color:#2e7d32"><?php echo htmlspecialchars((string)$success); ?></div><?php endif; ?>

    <!-- Tab Navigation -->
    <div style="display:flex;gap:0;margin-bottom:20px;border-bottom:2px solid #e0e0e0">
        <button onclick="switchTab('toppings')" id="tab-toppings" style="background:#FF6B35;color:white;padding:12px 24px;border:none;font-size:16px;font-weight:700;cursor:pointer;border-bottom:4px solid #FF6B35">🍜 Manage Toppings</button>
        <button onclick="switchTab('drinks')" id="tab-drinks" style="background:transparent;color:#666;padding:12px 24px;border:none;font-size:16px;font-weight:700;cursor:pointer;border-bottom:4px solid transparent">🍹 Manage Minuman</button>
    </div>

    <!-- Tab Content Toppings -->
    <div id="content-toppings">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
            <div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
                <h3>Tambah Topping Baru</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_item">
                    <input type="hidden" name="category" value="topping">
                    <div style="margin-bottom:8px"><label>Nama *</label><input name="name" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div>
                    <div style="display:flex;gap:8px;margin-bottom:8px"><div style="flex:1"><label>Harga *</label><input name="price" type="number" step="0.01" min="0" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div><div style="flex:1"><label>Stok *</label><input name="stock" type="number" min="0" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div>
                        <input type="hidden" name="unit" value="pcs">
                    </div>
                    <div style="margin-bottom:8px"><label>Gambar</label><input type="file" name="image" accept="image/*" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div>
                    <button type="submit" style="background:#FF6B35;color:white;padding:10px 14px;border-radius:8px;border:none">➕ Tambah Topping</button>
                </form>
            </div>
            <div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
                <h3>Info Topping</h3>
                <p>Topping adalah bahan tambahan untuk seblak seperti telur, bakso, sosis, dll.</p>
                <p>Total Topping: <strong><?php echo count($toppings); ?></strong></p>
            </div>
        </div>

        <div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
            <h3>Daftar Topping</h3>
            <?php if (empty($toppings)): ?>
                <p>Tidak ada topping. Mulai dengan menambah topping di atas.</p>
            <?php else: ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
                <?php foreach ($toppings as $t): ?>
                    <div style="background:#FFF5F0;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);display:flex;flex-direction:column">
                        <img src="../<?php echo htmlspecialchars($t['image'] ?? 'assets/seblak1.jpeg'); ?>" style="width:100%;height:150px;object-fit:cover;background:#f0f0f0" onerror="this.src='../assets/seblak1.jpeg'">
                        <div style="padding:12px;flex:1;display:flex;flex-direction:column">
                            <h4 style="margin:0 0 8px 0;font-size:16px;color:#333"><?php echo htmlspecialchars($t['name'] ?? ''); ?></h4>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #eee;flex:1">
                                <span style="color:#FF6B35;font-weight:700">Rp <?php echo number_format($t['price'],0,',','.'); ?></span>
                                <span style="background:#e3f2fd;color:#1976d2;padding:2px 8px;border-radius:4px;font-size:12px">Stok: <?php echo (int)$t['stock']; ?> <?php echo htmlspecialchars($t['unit'] ?? 'pcs'); ?></span>
                            </div>
                            <div style="display:flex;gap:6px">
                                <button onclick="editItem(<?php echo (int)$t['id']; ?>, 'topping')" style="flex:1;background:#FF9800;color:white;padding:6px;border:none;border-radius:4px;cursor:pointer;font-size:13px">✏️ Edit</button>
                                <a href="?delete=<?php echo $t['id']; ?>&type=topping" onclick="return confirm('Hapus topping ini?')" style="flex:1;background:#f44336;color:white;padding:6px;border:none;border-radius:4px;text-align:center;font-size:13px;text-decoration:none;cursor:pointer">🗑️ Hapus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab Content Minuman -->
    <div id="content-drinks" style="display:none">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
            <div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
                <h3>Tambah Minuman Baru</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_item">
                    <input type="hidden" name="category" value="drink">
                    <div style="margin-bottom:8px"><label>Nama *</label><input name="name" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div>
                    <div style="display:flex;gap:8px;margin-bottom:8px"><div style="flex:1"><label>Harga *</label><input name="price" type="number" step="0.01" min="0" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div><div style="flex:1"><label>Stok *</label><input name="stock" type="number" min="0" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div>
                        <input type="hidden" name="unit" value="pcs">
                    </div>
                    <div style="margin-bottom:8px"><label>Gambar</label><input type="file" name="image" accept="image/*" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></div>
                    <button type="submit" style="background:#FF6B35;color:white;padding:10px 14px;border-radius:8px;border:none">➕ Tambah Minuman</button>
                </form>
            </div>
            <div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
                <h3>Info Minuman</h3>
                <p>Minuman adalah produk tambahan seperti es jeruk, es teh, jus, dll.</p>
                <p>Total Minuman: <strong><?php echo count($menu_items); ?></strong></p>
            </div>
        </div>

        <div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
            <h3>Daftar Minuman</h3>
            <?php if (empty($menu_items)): ?>
                <p>Tidak ada minuman. Mulai dengan menambah minuman di atas.</p>
            <?php else: ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
                <?php foreach ($menu_items as $m): ?>
                    <div style="background:#FFF5F0;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);display:flex;flex-direction:column">
                        <img src="../<?php echo htmlspecialchars($m['image'] ?? 'assets/seblak1.jpeg'); ?>" style="width:100%;height:150px;object-fit:cover;background:#f0f0f0" onerror="this.src='../assets/seblak1.jpeg'">
                        <div style="padding:12px;flex:1;display:flex;flex-direction:column">
                            <h4 style="margin:0 0 8px 0;font-size:16px;color:#333"><?php echo htmlspecialchars($m['name'] ?? ''); ?></h4>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #eee;flex:1">
                                <span style="color:#FF6B35;font-weight:700">Rp <?php echo number_format($m['price'],0,',','.'); ?></span>
                                <span style="background:#e3f2fd;color:#1976d2;padding:2px 8px;border-radius:4px;font-size:12px">Stok: <?php echo (int)$m['stock']; ?> <?php echo htmlspecialchars($m['unit'] ?? 'pcs'); ?></span>
                            </div>
                            <div style="display:flex;gap:6px">
                                <button onclick="editItem(<?php echo (int)$m['id']; ?>, 'menu')" style="flex:1;background:#FF9800;color:white;padding:6px;border:none;border-radius:4px;cursor:pointer;font-size:13px">✏️ Edit</button>
                                <a href="?delete=<?php echo $m['id']; ?>&type=menu" onclick="return confirm('Hapus minuman ini?')" style="flex:1;background:#f44336;color:white;padding:6px;border:none;border-radius:4px;text-align:center;font-size:13px;text-decoration:none;cursor:pointer">🗑️ Hapus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal Edit -->
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;overflow:auto">
    <div style="background:white;margin:50px auto;padding:24px;border-radius:12px;max-width:500px;box-shadow:0 10px 40px rgba(0,0,0,0.2)">
        <h2 id="editTitle">Edit Item</h2>
        <form method="POST" enctype="multipart/form-data" id="editForm">
            <input type="hidden" name="action" value="edit_item">
            <input type="hidden" name="item_id" id="editItemId">
            <input type="hidden" name="item_type" id="editItemType">
            
            <div style="margin-bottom:12px">
                <label>Nama *</label>
                <input name="name" id="editName" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;box-sizing:border-box">
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label>Harga *</label>
                    <input name="price" id="editPrice" type="number" step="0.01" min="0" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;box-sizing:border-box">
                </div>
                <div>
                    <label>Stok</label>
                    <input name="stock" id="editStock" type="number" min="0" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;box-sizing:border-box">
                </div>
            </div>
            
            <div style="margin-bottom:12px" id="unitFieldContainer">
                <label>Satuan</label>
                <select name="unit" id="editUnit" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;box-sizing:border-box">
                    <option value="pcs">pcs</option>
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                    <option value="l">L</option>
                    <option value="ml">ml</option>
                    <option value="box">box</option>
                </select>
            </div>
            
            <div style="margin-bottom:12px;display:none" id="imageFieldContainer">
                <label>Gambar</label>
                <div style="margin-bottom:8px;width:100%;height:120px;background:#f5f5f5;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center">
                    <img id="editImagePreview" src="" style="width:100%;height:100%;object-fit:cover" onerror="this.src='../assets/seblak1.jpeg'">
                </div>
                <input type="file" name="image" id="editImage" accept="image/*" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;box-sizing:border-box">
            </div>
            
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button type="button" onclick="closeEditModal()" style="padding:10px 16px;border-radius:6px;border:1px solid #ddd;background:white;cursor:pointer">Batal</button>
                <button type="submit" style="padding:10px 16px;border-radius:6px;border:none;background:#FF6B35;color:white;cursor:pointer">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Data untuk edit
var editData = {
    <?php
    echo "'toppings': " . json_encode($toppings) . ",\n";
    echo "'menu': " . json_encode($menu_items);
    ?>
};

function editItem(id, type) {
    var item = null;
    var dataKey = (type === 'topping') ? 'toppings' : 'menu';
    
    for (var i = 0; i < editData[dataKey].length; i++) {
        if (editData[dataKey][i].id == id) {
            item = editData[dataKey][i];
            break;
        }
    }
    
    if (!item) {
        alert('Item tidak ditemukan!');
        return;
    }
    
    document.getElementById('editItemId').value = item.id;
    document.getElementById('editItemType').value = type;
    document.getElementById('editName').value = item.name || '';
    document.getElementById('editPrice').value = item.price || 0;
    document.getElementById('editStock').value = item.stock || 0;
    // Tampilkan atau sembunyikan field satuan tergantung tipe
    var unitFieldContainer = document.getElementById('unitFieldContainer');
    if (type === 'topping') {
        document.getElementById('editUnit').value = item.unit || 'pcs';
        if (unitFieldContainer) unitFieldContainer.style.display = 'block';
    } else {
        // untuk minuman, paksa 'pcs' dan sembunyikan pilihan
        if (unitFieldContainer) unitFieldContainer.style.display = 'none';
        document.getElementById('editUnit').value = 'pcs';
    }
    
    // Show image field untuk toppings dan minuman
    var imageField = document.getElementById('imageFieldContainer');
    imageField.style.display = 'block';
    var imagePath = item.image || 'assets/seblak1.jpeg';
    document.getElementById('editImagePreview').src = '../' + imagePath;
    document.getElementById('editImage').value = '';
    
    var typeLabel = (type === 'topping') ? 'Topping' : 'Minuman';
    document.getElementById('editTitle').textContent = 'Edit ' + typeLabel;
    
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('editForm').reset();
}

window.onclick = function(event) {
    var modal = document.getElementById('editModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function switchTab(tab) {
    document.getElementById('content-toppings').style.display = 'none';
    document.getElementById('content-drinks').style.display = 'none';
    document.getElementById('content-' + tab).style.display = 'block';
    
    document.getElementById('tab-toppings').style.background = 'transparent';
    document.getElementById('tab-toppings').style.color = '#666';
    document.getElementById('tab-toppings').style.borderBottom = '4px solid transparent';
    document.getElementById('tab-drinks').style.background = 'transparent';
    document.getElementById('tab-drinks').style.color = '#666';
    document.getElementById('tab-drinks').style.borderBottom = '4px solid transparent';
    
    if (tab === 'toppings') {
        document.getElementById('tab-toppings').style.background = '#FF6B35';
        document.getElementById('tab-toppings').style.color = 'white';
        document.getElementById('tab-toppings').style.borderBottom = '4px solid #FF6B35';
    } else {
        document.getElementById('tab-drinks').style.background = '#FF6B35';
        document.getElementById('tab-drinks').style.color = 'white';
        document.getElementById('tab-drinks').style.borderBottom = '4px solid #FF6B35';
    }
}

// Preview gambar saat dipilih
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('editImage');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('editImagePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

</body>
</html>

<?php if ($conn) $conn->close(); ?>
