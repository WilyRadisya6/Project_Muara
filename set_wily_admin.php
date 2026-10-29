<?php
/**
 * One-time admin creation/update script.
 * Usage (one-time):
 *  http://localhost/yummy_yamy_web/set_wily_admin.php?confirm=yes
 *
 * This will create or update the admin account:
 *  Email: wily.rn004@gmail.com
 *  Password: radisya6
 *
 * IMPORTANT: Delete this file immediately after use.
 */

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo 'To perform the action, add ?confirm=yes to the URL.';
    exit;
}

include __DIR__ . '/includes/database.php';

$email = 'wily.rn004@gmail.com';
$fullname = 'Wily Radisya';
$plain = 'radisya6';
$hashed = password_hash($plain, PASSWORD_BCRYPT);

// Check if admins table exists / create handled by includes/database.php

// Try to find existing admin
$stmt = $conn->prepare('SELECT id FROM admins WHERE email = ?');
if (!$stmt) {
    echo 'DB error: ' . htmlspecialchars($conn->error);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    // update password + fullname + is_active
    $row = $res->fetch_assoc();
    $id = $row['id'];
    $u = $conn->prepare('UPDATE admins SET fullname = ?, password = ?, is_active = 1 WHERE id = ?');
    if (!$u) {
        echo 'Prepare failed: ' . htmlspecialchars($conn->error);
        exit;
    }
    $u->bind_param('ssi', $fullname, $hashed, $id);
    if ($u->execute()) {
        echo 'Admin updated successfully: ' . htmlspecialchars($email) . '<br>';
        echo 'Password set to: <strong>' . htmlspecialchars($plain) . '</strong><br>';
        echo '<p>Now login at <a href="admin_login.php">Admin Login</a> and change the password.</p>';
    } else {
        echo 'Update failed: ' . htmlspecialchars($u->error);
    }
} else {
    // insert new admin
    $i = $conn->prepare('INSERT INTO admins (fullname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)');
    if (!$i) {
        echo 'Prepare failed: ' . htmlspecialchars($conn->error);
        exit;
    }
    $role = 'admin';
    $is_active = 1;
    $i->bind_param('ssssi', $fullname, $email, $hashed, $role, $is_active);
    if ($i->execute()) {
        echo 'Admin created successfully: ' . htmlspecialchars($email) . '<br>';
        echo 'Password set to: <strong>' . htmlspecialchars($plain) . '</strong><br>';
        echo '<p>Now login at <a href="admin_login.php">Admin Login</a> and change the password.</p>';
    } else {
        echo 'Insert failed: ' . htmlspecialchars($i->error);
    }
}

echo '<p><strong>SECURITY:</strong> Delete this file (`set_wily_admin.php`) immediately after confirming login.</p>';

?>
