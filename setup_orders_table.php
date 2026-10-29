<?php
// Setup tabel orders dan order_items
include 'db_connect.php';

if ($conn && !$conn->connect_error) {
    // Tabel orders
    $create_orders = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        customer_name VARCHAR(100),
        customer_phone VARCHAR(20),
        customer_email VARCHAR(100),
        total_price DECIMAL(10, 2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
        order_status ENUM('pending', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
        qr_code TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_order_number (order_number),
        INDEX idx_payment_status (payment_status),
        INDEX idx_order_status (order_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // Tabel order_items (untuk setiap porsi)
    $create_order_items = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        portion_number INT NOT NULL,
        topping_id INT,
        topping_name VARCHAR(100) NOT NULL,
        topping_price DECIMAL(10, 2) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        kuah VARCHAR(50),
        pedas_level VARCHAR(50),
        subtotal DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        INDEX idx_order_id (order_id),
        INDEX idx_portion_number (portion_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // Tabel order_drinks (untuk minuman)
    $create_order_drinks = "CREATE TABLE IF NOT EXISTS order_drinks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        drink_name VARCHAR(100) NOT NULL,
        drink_price DECIMAL(10, 2) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        subtotal DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        INDEX idx_order_id (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($create_orders)) {
        echo "Tabel orders berhasil dibuat/ada.<br>";
    } else {
        echo "Error creating orders table: " . $conn->error . "<br>";
    }
    
    if ($conn->query($create_order_items)) {
        echo "Tabel order_items berhasil dibuat/ada.<br>";
    } else {
        echo "Error creating order_items table: " . $conn->error . "<br>";
    }
    
    if ($conn->query($create_order_drinks)) {
        echo "Tabel order_drinks berhasil dibuat/ada.<br>";
    } else {
        echo "Error creating order_drinks table: " . $conn->error . "<br>";
    }
    
    echo "<br><a href='menu.php'>Kembali ke Menu</a>";
} else {
    echo "Database connection error!";
}
?>

