<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
session_destroy();

// Clear session data
$_SESSION = array();

// Redirect to login page
header('Location: login.php');
exit();
?>

