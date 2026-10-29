<?php
/**
 * Check and Create Admin Account
 * Run this to check if admin exists or create one
 */

include 'includes/database.php';

$page_title = 'Check Admin';
include 'includes/header.php';

// Check if admin exists
$query = "SELECT id, email, fullname FROM admins LIMIT 1";
$result = $conn->query($query);

$admin_exists = false;
$admin_info = null;

if ($result && $result->num_rows > 0) {
    $admin_exists = true;
    $admin_info = $result->fetch_assoc();
}

// Handle create admin
$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_admin'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $fullname = trim($_POST['fullname']);
    
    if (empty($email) || empty($password) || empty($fullname)) {
        $message = 'Semua field wajib diisi!';
        $status = 'error';
    } else {
        // Check if admin already exists
        $check_query = "SELECT id FROM admins WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = 'Admin dengan email ini sudah ada!';
            $status = 'error';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert admin
            $insert_query = "INSERT INTO admins (fullname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            
            $role = 'admin';
            $is_active = 1;
            
            $insert_stmt->bind_param("ssssi", $fullname, $email, $hashedPassword, $role, $is_active);
            
            if ($insert_stmt->execute()) {
                $message = 'Akun admin berhasil dibuat!';
                $status = 'success';
                $admin_exists = true;
                $admin_info = [
                    'email' => $email,
                    'fullname' => $fullname
                ];
            } else {
                $message = 'Gagal membuat akun admin: ' . $insert_stmt->error;
                $status = 'error';
            }
        }
    }
}
?>

<style>
    .check-admin-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 40px 20px;
    }
    
    .check-admin-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .check-admin-container h1 {
        color: #333;
        margin-top: 0;
        text-align: center;
    }
    
    .info-box {
        background: #FFF5F0;
        border-left: 4px solid #FF6B35;
        padding: 20px;
        border-radius: 6px;
        margin: 20px 0;
    }
    
    .info-box h3 {
        margin-top: 0;
        color: #FF6B35;
    }
    
    .credentials {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 6px;
        font-family: monospace;
        margin: 15px 0;
    }
    
    .credentials p {
        margin: 8px 0;
        font-size: 14px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #FF6B35;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: #fee;
        color: #c33;
        border: 1px solid #fcc;
    }
    
    .alert-success {
        background: #efe;
        color: #3c3;
        border: 1px solid #cfc;
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        display: inline-block;
        width: 100%;
    }
    
    .btn-primary {
        background: #FF6B35;
        color: white;
    }
    
    .btn-primary:hover {
        background: #E8572D;
    }
    
    .btn-secondary {
        background: #999;
        color: white;
        margin-top: 10px;
    }
    
    .btn-secondary:hover {
        background: #888;
    }
</style>

<section class="check-admin-section">
    <div class="check-admin-container">
        <h1>Check Admin Account</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($admin_exists && $admin_info): ?>
            <div class="info-box">
                <h3>✓ Admin Account Found</h3>
                <p>Akun admin sudah ada di database:</p>
                <div class="credentials">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($admin_info['email']); ?></p>
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($admin_info['fullname']); ?></p>
                </div>
                <p style="margin-top: 15px;">Silakan gunakan email di atas untuk login. Jika lupa password, buat akun admin baru di bawah.</p>
            </div>
            
            <a href="admin_login.php" class="btn btn-primary">Go to Admin Login</a>
        <?php else: ?>
            <div class="info-box">
                <h3>⚠ Admin Account Not Found</h3>
                <p>Tidak ada akun admin di database. Silakan buat akun admin baru:</p>
            </div>
        <?php endif; ?>
        
        <h2 style="margin-top: 30px; border-top: 2px solid #FFF5F0; padding-top: 20px;">Buat Akun Admin Baru</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="create_admin" value="1">
            
            <div class="form-group">
                <label for="fullname">Nama Lengkap *</label>
                <input type="text" id="fullname" name="fullname" placeholder="Administrator" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="admin@yummyyamy.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required minlength="6">
                <small style="color: #666; font-size: 12px;">Minimal 6 karakter</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Buat Akun Admin</button>
        </form>
        
        <a href="admin_login.php" class="btn btn-secondary">Kembali ke Login</a>
    </div>
</section>

<?php
include 'includes/footer.php';
?>

