<?php 
    session_start();
    $page_title = 'Sign In';
    include 'includes/header.php';
    include 'includes/database.php';
    
    $error = '';
    $success = '';
    $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : 'index.php';
    
    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            // Validate input
            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } else {
                // Create user object and attempt login
                $user = new User($conn);
                $user->email = $email;
                $user->password = $password;
                
                $result = $user->login();
                
                if ($result['success']) {
                    $success = $result['message'];
                    // Redirect to home or the requested page after 2 seconds
                    echo '<meta http-equiv="refresh" content="2;url=' . htmlspecialchars($redirect_url) . '">';
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
            <h1>Sign In</h1>
            <p class="auth-subtitle">Welcome back! Please sign in to your account.</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST" action="signin.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="primary-btn" style="width: 100%; padding: 16px;">Sign In</button>
            </form>
            
            <p class="auth-footer">
                Don't have an account? <a href="signup.php">Sign Up here</a>
            </p>
            <p class="auth-footer">
                <a href="#">Forgot your password?</a>
            </p>
        </div>
        
        <div class="auth-image">
            <svg width="300" height="300" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="150" cy="150" r="140" fill="#FFF5F0" stroke="#FF6B35" stroke-width="2"/>
                <path d="M120 140C120 125 130 115 145 115C160 115 170 125 170 140" stroke="#FF6B35" stroke-width="3" stroke-linecap="round" fill="none"/>
                <circle cx="130" cy="135" r="6" fill="#FF6B35"/>
                <circle cx="160" cy="135" r="6" fill="#FF6B35"/>
                <path d="M125 160C125 160 135 170 150 170C165 170 175 160 175 160" stroke="#FF6B35" stroke-width="3" stroke-linecap="round" fill="none"/>
            </svg>
        </div>
    </div>
</section>

<?php
    include 'includes/footer.php';
?>

