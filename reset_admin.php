<?php
/**
 * Temporary admin password reset script.
 * Usage (one-time):
 *  http://localhost/yummy_yamy_web/reset_admin.php?email=admin@yummyyamy.com&confirm=yes
 * This sets the admin password to `admin12345` (bcrypt hashed).
 * IMPORTANT: Delete this file immediately after use.
 */

include __DIR__ . '/includes/database.php';

if (!isset($_GET['email'])) {
    echo 'Missing email parameter. Example: ?email=admin@yummyyamy.com&confirm=yes';
    exit;
}

$email = $_GET['email'];

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo 'Add &confirm=yes to actually perform the reset.';
    exit;
}

// Prepare new password
$newPassword = 'admin12345';
$hashed = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
if (!$stmt) {
    echo 'Prepare failed: ' . htmlspecialchars($conn->error);
    exit;
}

$stmt->bind_param('ss', $hashed, $email);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo 'Password reset successful for ' . htmlspecialchars($email) . '.\n';
        echo 'New password: <strong>admin12345</strong>\n';
        echo '<p>Now go to <a href="admin_login.php">Admin Login</a> and sign in.</p>';
        echo '<p><strong>SECURITY:</strong> Delete this file now (reset_admin.php) after confirming login.</p>';
    } else {
        echo 'No admin found with that email.';
    }
} else {
    echo 'Execute failed: ' . htmlspecialchars($stmt->error);
}

?>