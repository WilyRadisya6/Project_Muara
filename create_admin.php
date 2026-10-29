<?php
/**
 * Create Default Admin Account
 * Run this once to create the initial admin account
 * Access: http://localhost/yummy_yamy_web/create_admin.php
 * 
 * Default Credentials:
 * Email: admin@yummyyamy.com
 * Password: admin12345
 * 
 * IMPORTANT: Delete this file after running it!
 */

include 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Hash password
    $hashedPassword = password_hash('radisya6', PASSWORD_BCRYPT);
    
    // Check if admin already exists
    $query = "SELECT id FROM admins WHERE email = 'wily.rn004@gmail.com'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $message = 'Admin account already exists!';
        $status = 'error';
    } else {
        // Insert admin
        $query = "INSERT INTO admins (fullname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        $fullname = 'Administrator';
        $email = 'wily.rn004@gmail.com';
        $role = 'admin';
        $is_active = 1;
        
        $stmt->bind_param("ssssi", $fullname, $email, $hashedPassword, $role, $is_active);
        
        if ($stmt->execute()) {
            $message = 'Admin account created successfully!';
            $status = 'success';
        } else {
            $message = 'Failed to create admin account: ' . $stmt->error;
            $status = 'error';
        }
    }
}

$page_title = 'Create Admin Account';
include 'includes/header.php';
?>

<style>
    .setup-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 40px 20px;
    }
    
    .setup-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .setup-container h1 {
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
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        display: inline-block;
    }
    
    .btn-confirm {
        background: #FF6B35;
        color: white;
    }
    
    .btn-confirm:hover {
        background: #E8572D;
    }
    
    .btn-cancel {
        background: #999;
        color: white;
    }
    
    .btn-cancel:hover {
        background: #888;
    }
</style>

<section class="setup-section">
    <div class="setup-container">
        <h1>Create Admin Account</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!isset($message)): ?>
            <div class="info-box">
                <h3>Admin Account Setup</h3>
                <p>This will create a new administrator account with the following credentials:</p>
            </div>
            
            <div class="credentials">
                <p><strong>Email:</strong> admin@yummyyamy.com</p>
                <p><strong>Password:</strong> admin12345</p>
            </div>
            
            <div class="info-box">
                <h3>Security Notice</h3>
                <p>Please remember to:</p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Change the admin password after first login</li>
                    <li>Delete this file (create_admin.php) after setup</li>
                    <li>Keep the admin email secure</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <a href="?confirm=yes" class="btn btn-confirm">Create Admin Account</a>
                <a href="index.php" class="btn btn-cancel">Cancel</a>
            </div>
        <?php else: ?>
            <div class="action-buttons" style="margin-top: 30px;">
                <?php if ($status === 'success'): ?>
                    <a href="admin_login.php" class="btn btn-confirm">Go to Admin Login</a>
                <?php else: ?>
                    <a href="index.php" class="btn btn-cancel">Back to Home</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
