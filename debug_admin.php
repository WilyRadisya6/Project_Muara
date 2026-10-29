<?php
/**
 * Debug Admin Login Issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/database.php';

echo "<h1>Admin Login Debug</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; } .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }</style>";

// 1. Check database connection
echo "<h2>1. Database Connection</h2>";
if ($conn && !$conn->connect_error) {
    echo "<p class='success'>✓ Database connected</p>";
} else {
    echo "<p class='error'>✗ Database connection failed: " . ($conn->connect_error ?? 'Unknown error') . "</p>";
    exit;
}

// 2. Check if admins table exists
echo "<h2>2. Check Admins Table</h2>";
$table_check = $conn->query("SHOW TABLES LIKE 'admins'");
if ($table_check && $table_check->num_rows > 0) {
    echo "<p class='success'>✓ Table 'admins' exists</p>";
} else {
    echo "<p class='error'>✗ Table 'admins' does not exist</p>";
    echo "<p>Creating table...</p>";
    
    $create_table = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        is_active TINYINT DEFAULT 1,
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($create_table)) {
        echo "<p class='success'>✓ Table 'admins' created</p>";
    } else {
        echo "<p class='error'>✗ Failed to create table: " . $conn->error . "</p>";
    }
}

// 3. Check existing admins
echo "<h2>3. Existing Admins</h2>";
$result = $conn->query("SELECT id, fullname, email, is_active, created_at FROM admins");
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✓ Found " . $result->num_rows . " admin(s):</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Active</th><th>Created</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>✗ No admins found in database</p>";
}

// 4. Create test admin
echo "<h2>4. Create Test Admin</h2>";
$test_email = 'admin@yummyyamy.com';
$test_password = 'admin123';
$test_name = 'Administrator';

// Check if test admin exists
$check_query = "SELECT id, password FROM admins WHERE email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $test_email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $admin_row = $check_result->fetch_assoc();
    echo "<p class='info'>Admin dengan email '$test_email' sudah ada (ID: " . $admin_row['id'] . ")</p>";
    
    // Test password
    $test_hash = password_hash($test_password, PASSWORD_BCRYPT);
    $verify = password_verify($test_password, $admin_row['password']);
    
    echo "<p>Password hash di database: " . substr($admin_row['password'], 0, 20) . "...</p>";
    echo "<p>Test password verification: " . ($verify ? "<span class='success'>✓ Password cocok</span>" : "<span class='error'>✗ Password tidak cocok</span>") . "</p>";
    
    // Update password
    echo "<p>Updating password...</p>";
    $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
    $update_query = "UPDATE admins SET password = ?, is_active = 1 WHERE email = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ss", $new_hash, $test_email);
    
    if ($update_stmt->execute()) {
        echo "<p class='success'>✓ Password updated</p>";
    } else {
        echo "<p class='error'>✗ Failed to update password: " . $update_stmt->error . "</p>";
    }
} else {
    echo "<p>Creating new admin...</p>";
    $hashedPassword = password_hash($test_password, PASSWORD_BCRYPT);
    $insert_query = "INSERT INTO admins (fullname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    
    $role = 'admin';
    $is_active = 1;
    
    $insert_stmt->bind_param("ssssi", $test_name, $test_email, $hashedPassword, $role, $is_active);
    
    if ($insert_stmt->execute()) {
        echo "<p class='success'>✓ Admin created successfully!</p>";
        echo "<div class='info'>";
        echo "<h3>Login Credentials:</h3>";
        echo "<p><strong>Email:</strong> $test_email</p>";
        echo "<p><strong>Password:</strong> $test_password</p>";
        echo "</div>";
    } else {
        echo "<p class='error'>✗ Failed to create admin: " . $insert_stmt->error . "</p>";
    }
}

// 5. Test login function
echo "<h2>5. Test Login Function</h2>";
$admin = new Admin($conn);
$admin->email = $test_email;
$admin->password = $test_password;

$login_result = $admin->login();

if ($login_result['success']) {
    echo "<p class='success'>✓ Login test successful!</p>";
    echo "<p>Admin ID: " . $login_result['admin_id'] . "</p>";
} else {
    echo "<p class='error'>✗ Login test failed: " . $login_result['message'] . "</p>";
}

// 6. Final credentials
echo "<h2>6. Final Login Credentials</h2>";
echo "<div class='info' style='background: #e8f5e9; border: 2px solid #4caf50; padding: 20px;'>";
echo "<h3 style='margin-top: 0; color: #2e7d32;'>Gunakan kredensial ini untuk login:</h3>";
echo "<p style='font-size: 18px;'><strong>Email:</strong> <code>$test_email</code></p>";
echo "<p style='font-size: 18px;'><strong>Password:</strong> <code>$test_password</code></p>";
echo "<p><a href='admin_login.php' style='background: #FF6B35; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Go to Admin Login</a></p>";
echo "</div>";

$conn->close();
?>

