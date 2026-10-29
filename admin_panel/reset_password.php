<?php 
    session_start();
    
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        header('Location: login.php');
        exit();
    }
    
    $page_title = 'Reset User Password';
    include '../includes/config.php';
    include '../includes/database.php';
    
    if (!isset($_GET['id'])) {
        header('Location: dashboard.php');
        exit();
    }
    
    $user_id = intval($_GET['id']);
    $admin = new Admin($conn);
    $user = $admin->getUserById($user_id);
    
    if (!$user) {
        header('Location: dashboard.php');
        exit();
    }
    
    $error = '';
    $success = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
        $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
        
        if (empty($new_password) || empty($confirm_password)) {
            $error = 'Please fill in both password fields.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            $result = $admin->resetUserPassword($user_id, $new_password);
            if ($result['success']) {
                $success = $result['message'];
                echo '<meta http-equiv="refresh" content="2;url=dashboard.php">';
            } else {
                $error = $result['message'];
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset User Password - Yummy Yamy Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
</head>
<body>

<style>
    .reset-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 40px 20px;
    }
    
    .reset-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .reset-container h1 {
        color: #333;
        margin-top: 0;
        font-family: 'Playfair Display', serif;
    }
    
    .user-info {
        background: #FFF5F0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #FF6B35;
    }
    
    .user-info p {
        margin: 5px 0;
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
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #FF6B35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .alert-error {
        background: #ffebee;
        color: #c62828;
        border-left: 4px solid #f44336;
    }
    
    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        border-left: 4px solid #4CAF50;
    }
</style>

<section class="reset-section">
    <div class="reset-container">
        <h1>Reset User Password</h1>
        
        <div class="user-info">
            <p><strong>User:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
            </div>
            
            <button type="submit" class="primary-btn" style="width: 100%; padding: 12px;">Reset Password</button>
            <a href="dashboard.php" class="primary-btn" style="display: inline-block; width: calc(100% - 2px); padding: 12px; text-align: center; background: #999; margin-top: 10px; text-decoration: none;">Cancel</a>
        </form>
    </div>
</section>

</body>
</html>

