<?php 
    session_start();
    
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
        header('Location: admin/dashboard.php');
        exit();
    }
    
    $page_title = 'Admin Login';
    include 'includes/header.php';
    include 'includes/database.php';
    
    $error = '';
    $success = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } else {
                $admin = new Admin($conn);
                $admin->email = $email;
                $admin->password = $password;
                
                $result = $admin->login();
                
                if ($result['success']) {
                    $success = $result['message'];
                    echo '<meta http-equiv="refresh" content="2;url=admin/dashboard.php">';
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
?>

<section class="auth-section">
    <div class="auth-container">
        <div class="auth-form-wrapper">
            <h1>Admin Login</h1>
            <p class="auth-subtitle">Enter your admin credentials to access the dashboard.</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST" action="admin_login.php">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" placeholder="admin@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="primary-btn" style="width: 100%; padding: 16px;">Login as Admin</button>
            </form>
            
            <p class="auth-footer">
                <a href="create_simple_admin.php" style="color: #FF6B35; font-weight: 600;">Buat Akun Admin Baru</a> | 
                <a href="index.php">Back to Home</a>
            </p>
        </div>
        
        <div class="auth-image">
            <svg width="300" height="300" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="150" cy="150" r="140" fill="#FFF5F0" stroke="#FF6B35" stroke-width="2"/>
                <path d="M120 140C120 125 130 115 145 115C160 115 170 125 170 140" stroke="#FF6B35" stroke-width="3" stroke-linecap="round" fill="none"/>
                <circle cx="130" cy="135" r="6" fill="#FF6B35"/>
                <circle cx="160" cy="135" r="6" fill="#FF6B35"/>
                <path d="M125 160C125 160 135 170 150 170C165 170 175 160 175 160" stroke="#FF6B35" stroke-width="3" stroke-linecap="round" fill="none"/>
                <!-- Shield icon for admin -->
                <path d="M100 80 L100 120 Q100 160 150 180 Q200 160 200 120 L200 80 Z" stroke="#FF6B35" stroke-width="2" fill="none"/>
            </svg>
        </div>
    </div>
</section>

<?php
    include 'includes/footer.php';
?>
