<?php
session_start();

// Cek apakah admin sudah login, jika tidak redirect ke login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// Halaman `manage_toppings.php` telah digabungkan ke `manage_menu.php`.
// Redirect ke halaman baru agar semua link tetap berfungsi.
header('Location: manage_menu.php');
exit();
?>
<?php
session_start();

// Cek apakah admin sudah login, jika tidak redirect ke login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// Halaman `manage_toppings.php` telah digabungkan ke `manage_menu.php`.
// Redirect ke halaman baru agar semua link tetap berfungsi.
header('Location: manage_menu.php');
exit();

$page_title = 'Manage Toppings';
include '../db_connect.php';

$error = '';
$success = '';

// Buat tabel toppings jika belum ada
$create_table = "CREATE TABLE IF NOT EXISTS toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn && !$conn->connect_error) {
    $conn->query($create_table);
}

// Handle tambah topping
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    // Handle upload gambar
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/toppings/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $new_filename = 'topping_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'assets/toppings/' . $new_filename;
            } else {
                $error = 'Gagal mengupload gambar.';
            }
        } else {
            $error = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.';
        }
    } else {
        $error = 'Gambar wajib diupload.';
    }
    
    if (empty($error) && !empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO toppings (name, price, stock, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $name, $price, $stock, $image_path);
        
        if ($stmt->execute()) {
            $success = 'Topping berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan topping: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle update stok
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_stock') {
    $topping_id = intval($_POST['topping_id']);
    $new_stock = intval($_POST['new_stock']);
    
    $stmt = $conn->prepare("UPDATE toppings SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_stock, $topping_id);
    
    if ($stmt->execute()) {
        $success = 'Stok berhasil diupdate!';
    } else {
        $error = 'Gagal mengupdate stok: ' . $stmt->error;
    }
    $stmt->close();
}

// Handle hapus topping
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $topping_id = intval($_GET['delete']);
    
    // Ambil path gambar untuk dihapus
    $stmt = $conn->prepare("SELECT image FROM toppings WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $topping_id);
        if ($stmt->execute()) {
            $stmt->bind_result($image_path_to_delete);
            $stmt->fetch();
        } else {
            $error = 'Gagal mengambil data gambar: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = 'Gagal menyiapkan query: ' . $conn->error;
    }

    // Hapus file gambar jika ada
    if (!empty($image_path_to_delete)) {
        // gambar disimpan relatif terhadap root proyek seperti 'assets/toppings/xxx'
        $full_image_path = __DIR__ . '/../' . $image_path_to_delete;
        if (file_exists($full_image_path)) {
            @unlink($full_image_path);
        }
    }

    // Hapus record dari DB
    $stmt = $conn->prepare("DELETE FROM toppings WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $topping_id);
        if ($stmt->execute()) {
            $success = 'Topping berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus topping: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = 'Gagal menyiapkan query hapus: ' . $conn->error;
    }
}

?>
<style>
.btn-small {
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.btn-small:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

.btn-danger {
    background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
    color: white;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
    color: white;
    text-decoration: none;
}

.manage-toppings-actions {
    text-align: center;
    margin-top: 40px;
}

.secondary-btn {
    display: inline-block;
    padding: 12px 24px;
    background: white;
    color: #FF6B35;
    border: 2px solid #FF6B35;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.secondary-btn:hover {
    background: #FF6B35;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

#imageUploadArea:hover {
    border-color: #FF8C5A;
    background: #fff;
}

.stock-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

.stock-high {
    background: #e8f5e9;
    color: #2e7d32;
}

.stock-medium {
    background: #fff3cd;
    color: #856404;
}

.stock-low {
    background: #ffebee;
    color: #c62828;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .manage-toppings-table {
        font-size: 12px;
    }
    
    .manage-toppings-table thead th,
    .manage-toppings-table tbody td {
        padding: 12px 8px;
    }
    
    .inline-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stock-input {
        width: 100%;
    }
}
</style>

<!-- Admin Navbar -->
<nav class="admin-navbar">
    <div class="admin-navbar-left">
        <a href="dashboard.php" class="admin-navbar-logo">
            <img src="../assets/logoyummy.png" alt="Yummy Yamy Logo">
            <span>Yummy Yamy Admin</span>
        </a>
    </div>
    <div class="admin-navbar-right">
        <a href="dashboard.php" class="admin-navbar-link">Dashboard</a>
        <a href="manage_toppings.php" class="admin-navbar-link active">Manage Toppings</a>
        <a href="scan_qr.php" class="admin-navbar-link">Scan QR</a>
        <a href="logout.php" class="admin-navbar-link" style="background: #FF6B35; color: white;">Logout</a>
    </div>
</nav>

<section class="manage-toppings-section">
    <div class="manage-toppings-container">
        <div class="manage-toppings-header">
            <h1>Manage Toppings</h1>
            <p>Kelola topping seblak prasmanan dengan mudah</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <!-- Form Tambah Topping -->
        <div class="manage-toppings-card">
            <h2>Tambah Topping Baru</h2>
            <div style="background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%); padding: 20px; border-radius: 12px; margin-bottom: 25px; border-left: 4px solid #FF6B35;">
                <p style="margin: 0; color: #666; font-size: 14px;">📝 Lengkapi form di bawah untuk menambahkan topping baru ke menu</p>
            </div>
            <form method="POST" enctype="multipart/form-data" class="manage-toppings-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="name">Nama Topping *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stok Awal *</label>
                        <input type="number" id="stock" name="stock" min="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image">Gambar Topping *</label>
                    <div style="border: 2px dashed #FF6B35; border-radius: 12px; padding: 20px; text-align: center; background: #FFF5F0; transition: all 0.3s ease;" id="imageUploadArea">
                        <input type="file" id="image" name="image" accept="image/*" required style="display: none;" onchange="previewImage(this)">
                        <label for="image" style="cursor: pointer; display: block;">
                            <div style="font-size: 48px; margin-bottom: 10px;">📷</div>
                            <div style="color: #FF6B35; font-weight: 600; margin-bottom: 5px;">Klik untuk Upload Gambar</div>
                            <div style="color: #666; font-size: 12px;">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                        </label>
                        <div id="imagePreview" style="display: none; margin-top: 15px;">
                            <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #FF6B35;">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="primary-btn" style="padding: 16px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);">
                    ➕ Tambah Topping
                </button>
            </form>
        </div>
        
        <!-- Daftar Topping -->
        <div class="manage-toppings-card">
            <h2>Daftar Topping</h2>
            
            <?php
            // Ensure $toppings is populated (fetch from DB if not already)
            if (!isset($toppings)) {
                $toppings = [];
                if ($conn && !$conn->connect_error) {
                    $res = $conn->query("SELECT * FROM toppings ORDER BY name ASC");
                    if ($res) {
                        while ($row = $res->fetch_assoc()) {
                            $toppings[] = $row;
                        }
                        $res->free();
                    }
                }
            }
            ?>
    
            <?php if (empty($toppings)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 64px; margin-bottom: 20px;">🍜</div>
                    <p style="color: #999; font-size: 16px; margin: 0;">Belum ada topping yang tersedia. Tambahkan topping pertama Anda!</p>
                </div>
            <?php else: ?>
                <div class="manage-toppings-table-wrapper" style="overflow-x:auto;">
                    <table class="manage-toppings-table" style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($toppings as $topping): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($topping['image'])): ?>
                                            <img src="<?php echo htmlspecialchars('../' . ltrim($topping['image'], '/')); ?>" alt="<?php echo htmlspecialchars($topping['name']); ?>" style="max-width:80px; border-radius:6px; border:1px solid #eee;">
                                        <?php else: ?>
                                            <div style="width:80px; height:50px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; color:#999; border-radius:6px;">No Image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($topping['name']); ?></strong>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; color: #FF6B35; font-size: 15px;">
                                            Rp <?php echo number_format($topping['price'], 0, ',', '.'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <form method="POST" class="inline-form" style="display:flex; gap:8px; align-items:center;">
                                                <input type="hidden" name="action" value="update_stock">
                                                <input type="hidden" name="topping_id" value="<?php echo $topping['id']; ?>">
                                                <input type="number" 
                                                       name="new_stock" 
                                                       value="<?php echo $topping['stock']; ?>" 
                                                       min="0" 
                                                       class="stock-input"
                                                       required
                                                       style="width:80px; padding:6px; border-radius:6px; border:1px solid #ddd;">
                                                <button type="submit" class="btn-small" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white;">✅ Update</button>
                                            </form>
                                            <?php 
                                            $stock = intval($topping['stock']);
                                            $stockClass = $stock > 20 ? 'stock-high' : ($stock > 10 ? 'stock-medium' : 'stock-low');
                                            $stockText = $stock > 20 ? 'Tersedia' : ($stock > 10 ? 'Sedang' : 'Hampir Habis');
                                            ?>
                                            <span class="stock-badge <?php echo $stockClass; ?>"><?php echo $stockText; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="?delete=<?php echo $topping['id']; ?>" 
                                           class="btn-danger btn-small"
                                           onclick="return confirm('Yakin ingin menghapus topping ini?')">🗑️ Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="manage-toppings-actions">
            <a href="dashboard.php" class="secondary-btn">← Kembali ke Dashboard</a>
        </div>
    </div>
</section>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const uploadArea = document.getElementById('imageUploadArea');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            uploadArea.style.borderColor = '#4CAF50';
            uploadArea.style.background = '#e8f5e9';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>

<?php
if ($conn) {
    $conn->close();
}
?>

