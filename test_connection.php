<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$page_title = 'Database Connection Test';
include 'includes/header.php';
include 'includes/database.php';

$connection_success = true;
$tables_created = false;
$users_count = 0;
$admins_count = 0;

if ($conn) {
    $users_result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($users_result) {
        $users_count = $users_result->fetch_assoc()['count'];
    }
    
    $admins_result = $conn->query("SELECT COUNT(*) as count FROM admins");
    if ($admins_result) {
        $admins_count = $admins_result->fetch_assoc()['count'];
    }
    
    $tables_created = true;
}
?>

<style>
    .test-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 40px 20px;
    }
    
    .test-container {
        max-width: 700px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .test-container h1 {
        color: #333;
        margin-top: 0;
        text-align: center;
    }
    
    .status-box {
        background: #e8f5e9;
        border-left: 4px solid #2e7d32;
        padding: 20px;
        border-radius: 6px;
        margin: 20px 0;
    }
    
    .status-box.success {
        background: #e8f5e9;
        border-left-color: #2e7d32;
    }
    
    .status-box.error {
        background: #ffebee;
        border-left-color: #c62828;
    }
    
    .status-box h3 {
        margin-top: 0;
        color: #2e7d32;
        font-size: 18px;
    }
    
    .status-box.error h3 {
        color: #c62828;
    }
    
    .status-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
        font-size: 16px;
    }
    
    .status-icon {
        font-size: 20px;
        font-weight: bold;
    }
    
    .icon-success {
        color: #2e7d32;
    }
    
    .icon-error {
        color: #c62828;
    }
    
    .info-box {
        background: #FFF5F0;
        border-left: 4px solid #FF6B35;
        padding: 15px;
        border-radius: 6px;
        margin: 20px 0;
        font-size: 14px;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 30px;
    }
    
    .action-link {
        display: block;
        padding: 12px;
        text-align: center;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .action-link.primary {
        background: #FF6B35;
        color: white;
    }
    
    .action-link.primary:hover {
        background: #E8572D;
    }
    
    .action-link.secondary {
        background: #FF9B42;
        color: white;
    }
    
    .action-link.secondary:hover {
        background: #E88D36;
    }
    
    .action-link.tertiary {
        background: #999;
        color: white;
    }
    
    .action-link.tertiary:hover {
        background: #888;
    }
    
    .status-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .status-table td {
        padding: 10px;
        border: 1px solid #eee;
    }
    
    .status-table td:first-child {
        font-weight: 600;
        background: #f5f5f5;
        width: 40%;
    }
</style>

<section class="test-section">
    <div class="test-container">
        <h1>Database Connection Test</h1>
        
        <div class="status-box success">
            <h3>✓ Connection Status</h3>
            <div class="status-item">
                <span class="status-icon icon-success">✓</span>
                <span>Database: <strong><?php echo DB_NAME; ?></strong></span>
            </div>
            <div class="status-item">
                <span class="status-icon icon-success">✓</span>
                <span>Host: <strong><?php echo DB_HOST; ?></strong></span>
            </div>
            <div class="status-item">
                <span class="status-icon icon-success">✓</span>
                <span>User: <strong><?php echo DB_USER; ?></strong></span>
            </div>
        </div>
        
        <div class="info-box">
            <strong>Database successfully connected and initialized!</strong>
            <p style="margin: 10px 0 0 0;">The tables have been created automatically.</p>
        </div>
        
        <div class="status-box success">
            <h3>📊 Database Statistics</h3>
            <table class="status-table">
                <tr>
                    <td>Registered Users</td>
                    <td style="font-weight: 600; color: #FF6B35;"><?php echo $users_count; ?></td>
                </tr>
                <tr>
                    <td>Admin Accounts</td>
                    <td style="font-weight: 600; color: #FF6B35;"><?php echo $admins_count; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="info-box">
            <strong>Next Steps:</strong>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>Click "Create Admin Account" below</li>
                <li>Log in with admin credentials</li>
                <li>Delete <code>create_admin.php</code> file</li>
                <li>Change admin password</li>
            </ol>
        </div>
        
        <div class="action-buttons">
            <a href="create_admin.php" class="action-link primary">Create Admin</a>
            <a href="admin_login.php" class="action-link secondary">Admin Login</a>
            <a href="index.php" class="action-link tertiary">Home</a>
        </div>
    </div>
</section>

<?php
    include 'includes/footer.php';
?>

