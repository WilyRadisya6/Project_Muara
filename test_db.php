<?php
/**
 * Database Configuration Test
 * This script verifies that the database and tables are properly set up
 * Access it via: http://localhost/yummy_yamy_web/test_db.php
 */

session_start();
include 'includes/header.php';
include 'includes/database.php';

echo '<div style="max-width: 800px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
echo '<h2 style="color: #FF6B35;">Database Configuration Test</h2>';

// Test 1: Database Connection
echo '<div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;">';
echo '<h3 style="margin-top: 0; color: #333;">✓ Database Connection</h3>';
echo '<p><strong>Host:</strong> ' . DB_HOST . '</p>';
echo '<p><strong>User:</strong> ' . DB_USER . '</p>';
echo '<p><strong>Database:</strong> ' . DB_NAME . '</p>';
echo '<p style="color: #2e7d32; font-weight: 600;">Connected successfully!</p>';
echo '</div>';

// Test 2: Users Table
echo '<div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;">';
echo '<h3 style="margin-top: 0; color: #333;">✓ Users Table</h3>';

$query = "SHOW COLUMNS FROM users";
$result = $conn->query($query);

if ($result) {
    echo '<table style="width: 100%; border-collapse: collapse;">';
    echo '<thead><tr style="background: #FF6B35; color: white;">';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Field</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Type</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Null</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Key</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    
    while ($column = $result->fetch_assoc()) {
        echo '<tr style="border: 1px solid #ddd;">';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . $column['Field'] . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . $column['Type'] . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . $column['Null'] . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . ($column['Key'] ?: 'No') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
} else {
    echo '<p style="color: #c62828;">Error: ' . $conn->error . '</p>';
}

echo '</div>';

// Test 3: User Count
echo '<div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;">';
echo '<h3 style="margin-top: 0; color: #333;">✓ Registered Users</h3>';

$query = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$user_count = $row['count'];

echo '<p><strong>Total Registered Users:</strong> ' . $user_count . '</p>';

if ($user_count > 0) {
    echo '<p style="font-weight: 600; margin-top: 15px;">Registered Users:</p>';
    echo '<table style="width: 100%; border-collapse: collapse;">';
    echo '<thead><tr style="background: #FF6B35; color: white;">';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">ID</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Full Name</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Email</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Registered</th>';
    echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Status</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    
    $query = "SELECT id, fullname, email, created_at, is_active FROM users ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    while ($user = $result->fetch_assoc()) {
        echo '<tr style="border: 1px solid #ddd;">';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . $user['id'] . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($user['fullname']) . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($user['email']) . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . date('Y-m-d H:i', strtotime($user['created_at'])) . '</td>';
        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . ($user['is_active'] ? '✓ Active' : '✗ Inactive') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
}

echo '</div>';

// Test 4: Session Status
echo '<div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;">';
echo '<h3 style="margin-top: 0; color: #333;">✓ Session Status</h3>';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo '<p style="color: #2e7d32;"><strong>Logged In:</strong> Yes</p>';
    echo '<p><strong>User Name:</strong> ' . htmlspecialchars($_SESSION['user_name']) . '</p>';
    echo '<p><strong>Email:</strong> ' . htmlspecialchars($_SESSION['user_email']) . '</p>';
} else {
    echo '<p style="color: #FF9B42;"><strong>Logged In:</strong> No (guest user)</p>';
}

echo '</div>';

// Test 5: Quick Actions
echo '<div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;">';
echo '<h3 style="margin-top: 0; color: #333;">Quick Actions</h3>';
echo '<a href="signin.php" style="display: inline-block; padding: 10px 20px; background: #FF6B35; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">Go to Sign In</a>';
echo '<a href="signup.php" style="display: inline-block; padding: 10px 20px; background: #FF9B42; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">Go to Sign Up</a>';
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo '<a href="logout.php" style="display: inline-block; padding: 10px 20px; background: #c62828; color: white; text-decoration: none; border-radius: 4px;">Logout</a>';
}
echo '</div>';

echo '</div>';

include 'includes/footer.php';
?>
