<?php
// Include config
require_once __DIR__ . '/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page
$current_page = getActivePage();
$page_title = isset($page_title) ? $page_title : '';

// Session checks: user or admin
$is_user_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$is_admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// Determine display name (admin takes precedence)
$display_name = '';
if ($is_admin_logged_in) {
    $display_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : '';
} elseif ($is_user_logged_in) {
    $display_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
}
$display_name = trim($display_name);
// Normalize capitalization
if ($display_name) {
    $display_name = ucwords(strtolower($display_name));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPageTitle($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
    <script>
        // Mark fonts as loaded to prevent blocking
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function() {
                document.body.classList.add('fonts-loaded');
            });
        } else {
            setTimeout(function() {
                document.body.classList.add('fonts-loaded');
            }, 100);
        }
    </script>
</head>
<body>

    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php">
                    <img src="assets/logoyummy.png" alt="Yummy Yamy Logo" class="logo-icon">
                    <span class="logo-text">Yummy Yamy</span>
                </a>
            </div>
            <nav class="nav" aria-label="Main Navigation">
                <a href="index.php" class="nav-link <?php echo isActive('index.php'); ?>">Home</a>
                <a href="about.php" class="nav-link <?php echo isActive('about.php'); ?>">About</a>
                <a href="menu.php" class="nav-link <?php echo isActive('menu.php'); ?>">Menu</a>
                <a href="contact.php" class="nav-link <?php echo isActive('contact.php'); ?>">Contact</a>
                
                <div class="nav-right">
                    <?php if ($is_user_logged_in): ?>
                        <span class="user-greeting">Selamat datang, <?php echo htmlspecialchars($display_name); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($is_admin_logged_in): ?>
                        <a href="admin_panel/index.php" class="admin-toggle-btn" title="Masuk ke Panel Admin">⚙️ Admin Panel</a>
                    <?php endif; ?>
                    
                    <?php if ($is_user_logged_in || $is_admin_logged_in): ?>
                        <a href="logout.php" class="logout-btn">Logout</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <div class="header-decorative-shapes">
            <div class="splash-shape shape-1"></div>
            <div class="splash-shape shape-2"></div>
        </div>
    </header>

