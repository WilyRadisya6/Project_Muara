<?php 
    session_start();
    
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
        header('Location: dashboard.php');
        exit();
    }
    
    $page_title = 'Admin Login';
    include '../includes/config.php';
    include '../includes/database.php';
    
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
                    echo '<meta http-equiv="refresh" content="2;url=dashboard.php">';
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Yummy Yamy</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
</head>
<body>

<style>
    .auth-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    
    .auth-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        max-width: 1200px;
        width: 100%;
        align-items: center;
    }
    
    .auth-form-wrapper {
        background: white;
        padding: 50px;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        width: 100%;
    }
    
    .auth-image {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .auth-image svg {
        max-width: 100%;
        height: auto;
        width: 100%;
        max-width: 400px;
    }
    
    @media (max-width: 968px) {
        .auth-container {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .auth-image {
            display: none;
        }
        
        .auth-form-wrapper {
            padding: 40px 30px;
        }
    }
    
    @media (max-width: 480px) {
        .auth-form-wrapper {
            padding: 30px 20px;
        }
        
        .auth-section {
            padding: 20px 15px;
        }
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #FF6B35;
        box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
    }
    
    #togglePassword:hover {
        color: #FF6B35;
    }
    
    #togglePassword:active {
        transform: translateY(-50%) scale(0.95);
    }
    
    .auth-subtitle {
        color: #666;
        font-size: 14px;
        margin-bottom: 30px;
        display: block;
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 30px;
        font-size: 14px;
        color: #666;
    }
    
    .auth-footer a {
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .auth-footer a:hover {
        color: #FF6B35;
    }
</style>

<section class="auth-section">
    <div class="auth-container">
        <div class="auth-form-wrapper">
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="width: 100px; height: 100px; margin: 0 auto 20px; background: linear-gradient(135deg, #FF6B35 0%, #FF8C5A 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(255, 107, 53, 0.3);">
                    <img src="../assets/logoyummy.png" alt="Yummy Yamy Logo" style="width: 70px; height: 70px; object-fit: contain;">
                </div>
                <h1 style="font-family: 'Playfair Display', serif; color: #FF6B35; margin: 0; font-size: 36px;">Admin Panel</h1>
                <p style="color: #666; margin: 10px 0 0 0; font-size: 14px;">Yummy Yamy Management System</p>
            </div>
            <h2 style="margin-bottom: 10px; font-family: 'Playfair Display', serif; font-size: 28px; color: #333;">Admin Login</h2>
            <p class="auth-subtitle">Masukkan kredensial admin untuk mengakses dashboard.</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" placeholder="admin@example.com" required style="width: 100%; padding: 14px 18px; border: 2px solid rgba(0,0,0,0.1); border-radius: 12px; font-size: 14px; font-family: 'Inter', sans-serif; transition: all 0.3s ease;">
                </div>
                
                <div class="form-group" style="position: relative; margin-bottom: 20px;">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required style="width: 100%; padding: 14px 50px 14px 18px; border: 2px solid rgba(0,0,0,0.1); border-radius: 12px; font-size: 14px; font-family: 'Inter', sans-serif; transition: all 0.3s ease; box-sizing: border-box;">
                        <button type="button" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 20px; padding: 5px; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; transition: color 0.3s ease;" onclick="togglePasswordVisibility()" title="Toggle password visibility">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="primary-btn" style="width: 100%; padding: 16px; font-size: 16px; font-weight: 600; margin-top: 10px;">Login as Admin</button>
            </form>
            
            <script>
            function togglePasswordVisibility() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eyeIcon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.textContent = '🙈';
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.textContent = '👁️';
                }
            }
            </script>
            
            <p class="auth-footer">
                <a href="../create_simple_admin.php" style="color: #FF6B35; font-weight: 600;">Buat Akun Admin Baru</a> | 
                <a href="../index.php">Back to Website</a>
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

</body>
</html>

