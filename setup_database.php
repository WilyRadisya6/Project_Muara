<?php
// Database Setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yummy_yamy_db";

// Connect to MySQL (tanpa database dulu)
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✓ Database '$dbname' created or already exists\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
    exit;
}

// Close and reconnect to the database
$conn->close();
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
}

// Create toppings table
$toppings_table = "CREATE TABLE IF NOT EXISTS toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($toppings_table) === TRUE) {
    echo "✓ Table 'toppings' created or already exists\n";
} else {
    echo "Error creating toppings table: " . $conn->error . "\n";
}

// Create menu table
$menu_table = "CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    category VARCHAR(50) NOT NULL DEFAULT 'drink',
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($menu_table) === TRUE) {
    echo "✓ Table 'menu' created or already exists\n";
} else {
    echo "Error creating menu table: " . $conn->error . "\n";
}

$conn->close();

echo "\n✓ Database setup complete!\n";
echo "You can now use the system:\n";
echo "- Admin: http://localhost/admin_panel/manage_menu.php\n";
echo "- User: http://localhost/menu.php\n";
?>
