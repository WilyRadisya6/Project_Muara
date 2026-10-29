<?php
/**
 * Authentication Middleware
 * Include this file at the top of pages that require user authentication
 * Usage: include 'includes/auth_required.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Redirect to sign in page with return URL
    $return_url = urlencode($_SERVER['REQUEST_URI']);
    header("Location: signin.php?redirect=" . $return_url);
    exit();
}
?>
