<?php 
    session_start();
    $page_title = 'Sign Up';
    include 'includes/header.php';
    include 'includes/database.php';
    
    $error = '';
    $success = '';
    
    // Handle registration form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $confirm_password = isset($_POST['confirm-password']) ? trim($_POST['confirm-password']) : '';
        
        // Validate input
        if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } else {
            // Create user object and register
            $user = new User($conn);
            $user->fullname = $fullname;
            $user->email = $email;
            $user->phone = $phone;
            $user->password = $password;
            
            $result = $user->register();
            
            if ($result['success']) {
                $success = $result['message'];
                // Redirect to signin after 2 seconds
                echo '<meta http-equiv="refresh" content="2;url=signin.php">';
            } else {
                $error = $result['message'];
            }
        }
    }
?>

<section class="auth-section">
    <div class="auth-container">
        <div class="auth-form-wrapper">
            <h1>Sign Up</h1>
            <p class="auth-subtitle">Create your account and start enjoying our meals!</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST" action="signup.php">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Your full name" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+62 8xx xxxx xxxx" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                </div>
                
                <div class="form-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
                </div>
                
                <button type="submit" class="primary-btn" style="width: 100%; padding: 16px;">Create Account</button>
            </form>
            
            <p class="auth-footer">
                Already have an account? <a href="signin.php">Sign In here</a>
            </p>
        </div>
        
        <div class="auth-image">
            <svg width="300" height="300" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="150" cy="150" r="140" fill="#FFF5F0" stroke="#FF6B35" stroke-width="2"/>
                <path d="M130 120C130 105 140 95 150 95C160 95 170 105 170 120" stroke="#FF6B35" stroke-width="3" stroke-linecap="round" fill="none"/>
                <circle cx="125" cy="115" r="5" fill="#FF6B35"/>
                <circle cx="175" cy="115" r="5" fill="#FF6B35"/>
                <path d="M120 160C120 160 132 175 150 175C168 175 180 160 180 160" stroke="#FF6B35" stroke-width="3" stroke-linecap="round" fill="none"/>
                <circle cx="150" cy="140" r="4" fill="#FF6B35"/>
            </svg>
        </div>
    </div>
</section>

<?php
    include 'includes/footer.php';
?>

