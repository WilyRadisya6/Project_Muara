<?php
/**
 * Database Configuration and Connection
 * Database: yummy_yamy
 * Tables: admins only
 * 
 * Admin-only system - no public user registration
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Laragon default is empty
define('DB_NAME', 'yummy_yamy');

// Create initial connection WITHOUT database name
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Function to create database and tables if they don't exist
function initializeDatabase($conn) {
    // Create database if not exists
    $createDB = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($createDB)) {
        return false;
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Create admins table
    $createAdminTable = "CREATE TABLE IF NOT EXISTS admins (
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
    
    if (!$conn->query($createAdminTable)) {
        return false;
    }
    
    return true;
}

// Initialize database on first connection
if (!initializeDatabase($conn)) {
    die("Database initialization failed: " . $conn->error);
}

/**
 * Admin class for admin operations
 */
class Admin {
    private $conn;
    private $table = 'admins';
    
    public $id;
    public $fullname;
    public $email;
    public $password;
    public $role;
    public $is_active;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Admin login
     */
    public function login() {
        $query = "SELECT id, fullname, email, password, role, is_active FROM " . $this->table . " WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error');
        }
        
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            if (!$row['is_active']) {
                return array('success' => false, 'message' => 'Admin account is disabled.');
            }
            
            if (password_verify($this->password, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_name'] = $row['fullname'];
                $_SESSION['admin_role'] = $row['role'];
                $_SESSION['admin_logged_in'] = true;
                
                return array('success' => true, 'message' => 'Admin login successful!', 'admin_id' => $row['id']);
            } else {
                return array('success' => false, 'message' => 'Invalid password.');
            }
        } else {
            return array('success' => false, 'message' => 'Admin account not found.');
        }
    }
    
    /**
     * Get admin by ID
     */
    public function getAdminById($admin_id) {
        $query = "SELECT id, fullname, email, role, created_at, is_active FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get all admins
     */
    public function getAllAdmins() {
        $query = "SELECT id, fullname, email, role, created_at, is_active FROM " . $this->table . " ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        $admins = array();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $admins[] = $row;
            }
        }
        
        return $admins;
    }
    
    /**
     * Change admin password
     */
    public function changePassword($admin_id, $new_password) {
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        
        $query = "UPDATE " . $this->table . " SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $hashedPassword, $admin_id);
        
        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Password changed successfully.');
        } else {
            return array('success' => false, 'message' => 'Failed to change password.');
        }
    }
    
    /**
     * Deactivate admin account
     */
    public function deactivateAdmin($admin_id) {
        $query = "UPDATE " . $this->table . " SET is_active = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $admin_id);
        
        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Admin account deactivated.');
        } else {
            return array('success' => false, 'message' => 'Failed to deactivate admin account.');
        }
    }
    
    /**
     * Activate admin account
     */
    public function activateAdmin($admin_id) {
        $query = "UPDATE " . $this->table . " SET is_active = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $admin_id);
        
        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Admin account activated.');
        } else {
            return array('success' => false, 'message' => 'Failed to activate admin account.');
        }
    }
    
    /**
     * Get total user count
     */
    public function getUserCount() {
        // Check if users table exists
        $table_check = $this->conn->query("SHOW TABLES LIKE 'users'");
        if ($table_check && $table_check->num_rows > 0) {
            $result = $this->conn->query("SELECT COUNT(*) as total FROM users");
            if ($result) {
                $row = $result->fetch_assoc();
                return intval($row['total']);
            }
        }
        return 0;
    }
    
    /**
     * Get active user count
     */
    public function getActiveUserCount() {
        // Check if users table exists
        $table_check = $this->conn->query("SHOW TABLES LIKE 'users'");
        if ($table_check && $table_check->num_rows > 0) {
            $result = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
            if ($result) {
                $row = $result->fetch_assoc();
                return intval($row['total']);
            }
        }
        return 0;
    }
    
    /**
     * Get all users
     */
    public function getAllUsers() {
        $users = array();
        
        // Check if users table exists
        $table_check = $this->conn->query("SHOW TABLES LIKE 'users'");
        if ($table_check && $table_check->num_rows > 0) {
            $result = $this->conn->query("SELECT id, fullname, email, phone, created_at, is_active FROM users ORDER BY created_at DESC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }
            }
        }
        
        return $users;
    }
}

?>
