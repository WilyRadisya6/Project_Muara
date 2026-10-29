<?php
/**
 * Admin check utility
 * Visit: http://localhost/yummy_yamy_web/admin_check.php
 * Shows all rows in `admins` table so you can confirm admin email exists.
 * Remove this file after use.
 */

include __DIR__ . '/includes/database.php';

header('Content-Type: text/html; charset=utf-8');

echo '<h2>Admins in database</h2>';

$result = $conn->query("SELECT id, fullname, email, role, is_active, created_at FROM admins ORDER BY id DESC");
if (!$result) {
    echo '<p style="color: red;">Error reading admins table: ' . htmlspecialchars($conn->error) . '</p>';
    echo '<p>If the table does not exist, run <code>create_admin.php</code> first.</p>';
    exit;
}

if ($result->num_rows === 0) {
    echo '<p>No admin accounts found.</p>';
    echo '<p>To create default admin: <a href="create_admin.php">create_admin.php</a></p>';
    exit;
}

echo '<table border="1" cellpadding="8" cellspacing="0">';
echo '<thead><tr><th>ID</th><th>Full Name</th><th>Email</th><th>Role</th><th>Active</th><th>Created</th></tr></thead><tbody>';
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
    echo '<td>' . htmlspecialchars($row['fullname']) . '</td>';
    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
    echo '<td>' . htmlspecialchars($row['role']) . '</td>';
    echo '<td>' . ($row['is_active'] ? 'Yes' : 'No') . '</td>';
    echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';

echo '<hr>';
echo '<h3>Quick actions</h3>';
echo '<p>Reset default admin password to <code>admin12345</code> (temporary): <a href="reset_admin.php?email=admin@yummyyamy.com&confirm=yes">Reset admin password</a></p>';
echo '<p>After you confirm login, DELETE this file for security.</p>';

?>