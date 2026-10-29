<?php
/**
 * Simple Admin Creator
 * Create admin account directly
 */

include 'includes/database.php';

// Default credentials
$default_email = 'admin@yummyyamy.com';
$default_password = 'admin123';
$default_name = 'Administrator';

// Check if admin exists
$check_query = "SELECT id, email FROM admins WHERE email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $default_email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

$admin_exists = $check_result->num_rows > 0;
$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : $default_email;
    $password = isset($_POST['password']) ? trim($_POST['password']) : $default_password;
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : $default_name;
    
    if (empty($email) || empty($password)) {
        $message = 'Email dan password wajib diisi!';
        $status = 'error';
    } else {
        // Check if admin already exists
        $check_query = "SELECT id FROM admins WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update password if admin exists
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $update_query = "UPDATE admins SET password = ?, is_active = 1 WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ss", $hashedPassword, $email);
            
            if ($update_stmt->execute()) {
                $message = 'Password admin berhasil diupdate!';
                $status = 'success';
            } else {
                $message = 'Gagal mengupdate password: ' . $update_stmt->error;
                $status = 'error';
            }
        } else {
            // Create new admin
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insert_query = "INSERT INTO admins (fullname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            
            $role = 'admin';
            $is_active = 1;
            
            $insert_stmt->bind_param("ssssi", $fullname, $email, $hashedPassword, $role, $is_active);
            
            if ($insert_stmt->execute()) {
                $message = 'Akun admin berhasil dibuat!';
                $status = 'success';
                $admin_exists = true;
            } else {
                $message = 'Gagal membuat akun admin: ' . $insert_stmt->error;
                $status = 'error';
            }
        }
    }
}

$page_title = 'Create Admin';
include 'includes/header.php';
?>

<style>
    .simple-admin-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 40px 20px;
    }
    
    .simple-admin-container {
        max-width: 500px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .simple-admin-container h1 {
        color: #333;
        margin-top: 0;
        text-align: center;
        margin-bottom: 30px;
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
        margin: 15px 0;
        font-family: monospace;
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
        box-sizing: border-box;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #FF6B35;
    }
    
    .btn {
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
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
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    .btn-secondary:hover {
        background: #888;
    }
</style>

<section class="simple-admin-section">
    <div class="simple-admin-container">
        <h1>Create Admin Account</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($admin_exists && !isset($_POST['create'])): ?>
            <div class="info-box">
                <h3>Admin Already Exists</h3>
                <p>Admin dengan email <strong><?php echo htmlspecialchars($default_email); ?></strong> sudah ada.</p>
                <p>Anda bisa mengupdate password dengan mengisi form di bawah.</p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="create" value="1">
            
            <div class="form-group">
                <label for="fullname">Nama Lengkap</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($default_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($default_email); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($default_password); ?>" required minlength="6">
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Minimal 6 karakter</small>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <?php echo $admin_exists ? 'Update Password' : 'Create Admin Account'; ?>
            </button>
        </form>
        
        <?php if ($status === 'success'): ?>
            <div class="info-box" style="margin-top: 20px;">
                <h3>✓ Success!</h3>
                <div class="credentials">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email ?? $default_email); ?></p>
                    <p><strong>Password:</strong> <?php echo htmlspecialchars($password ?? $default_password); ?></p>
                </div>
                <p style="margin-top: 15px;">Gunakan kredensial di atas untuk login.</p>
            </div>
            
            <a href="admin_login.php" class="btn btn-secondary">Go to Admin Login</a>
        <?php else: ?>
            <a href="admin_login.php" class="btn btn-secondary">Back to Login</a>
        <?php endif; ?>
    </div>
</section>

<?php
include 'includes/footer.php';
?>

