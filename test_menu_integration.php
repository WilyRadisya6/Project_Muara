<?php
/**
 * Test Menu Integration
 * Verifikasi bahwa menu integration berfungsi dengan baik
 */

session_start();
include 'db_connect.php';

$test_results = [
    'database_connection' => false,
    'toppings_table' => false,
    'menu_table' => false,
    'toppings_data' => 0,
    'menu_data' => 0,
    'upload_dir' => false,
    'admin_session' => false,
    'errors' => []
];

// Test 1: Database Connection
if ($conn && !$conn->connect_error) {
    $test_results['database_connection'] = true;
} else {
    $test_results['errors'][] = 'Database connection failed: ' . ($conn->connect_error ?? 'Unknown error');
}

// Test 2: Toppings Table
if ($test_results['database_connection']) {
    $result = $conn->query("SHOW TABLES LIKE 'toppings'");
    if ($result && $result->num_rows > 0) {
        $test_results['toppings_table'] = true;
        
        // Count toppings
        $count_result = $conn->query("SELECT COUNT(*) as total FROM toppings");
        if ($count_result) {
            $row = $count_result->fetch_assoc();
            $test_results['toppings_data'] = $row['total'];
        }
    } else {
        $test_results['errors'][] = 'Toppings table not found';
    }
}

// Test 3: Menu Table
if ($test_results['database_connection']) {
    $result = $conn->query("SHOW TABLES LIKE 'menu'");
    if ($result && $result->num_rows > 0) {
        $test_results['menu_table'] = true;
        
        // Count menu items
        $count_result = $conn->query("SELECT COUNT(*) as total FROM menu");
        if ($count_result) {
            $row = $count_result->fetch_assoc();
            $test_results['menu_data'] = $row['total'];
        }
    } else {
        $test_results['errors'][] = 'Menu table not found';
    }
}

// Test 4: Upload Directory
$upload_dir = 'assets/menu_items/';
if (is_dir($upload_dir)) {
    $test_results['upload_dir'] = true;
} else {
    $test_results['errors'][] = 'Upload directory not found or not writable';
}

// Test 5: Admin Session
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    $test_results['admin_session'] = true;
} else {
    $test_results['errors'][] = 'Admin not logged in';
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Integration Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; }
        h1 { color: #333; margin-bottom: 24px; font-size: 28px; }
        .test-group { margin-bottom: 20px; }
        .test-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: #f9f9f9; border-radius: 6px; margin-bottom: 8px; border-left: 4px solid #ddd; }
        .test-item.pass { border-left-color: #4CAF50; background: #f1f8f4; }
        .test-item.fail { border-left-color: #f44336; background: #fef1f0; }
        .test-item.info { border-left-color: #2196F3; background: #f3f7fb; }
        .status-icon { font-size: 20px; min-width: 24px; }
        .test-label { flex: 1; }
        .test-value { font-weight: 600; color: #666; }
        .error-list { background: #ffebee; border-left: 4px solid #f44336; padding: 12px; border-radius: 6px; margin-top: 16px; }
        .error-list li { margin-left: 20px; color: #c62828; margin-bottom: 4px; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px; }
        .summary-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px; border-radius: 8px; text-align: center; }
        .summary-card.warn { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .summary-card.ok { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .summary-value { font-size: 24px; font-weight: bold; }
        .summary-label { font-size: 12px; opacity: 0.9; margin-top: 4px; }
        .action-buttons { display: flex; gap: 8px; margin-top: 16px; }
        .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 600; }
        .btn-primary { background: #FF6B35; color: white; }
        .btn-primary:hover { background: #ff5a1f; }
        .btn-secondary { background: #2196F3; color: white; }
        .btn-secondary:hover { background: #1976d2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Menu Integration Test</h1>
        
        <div class="summary">
            <div class="summary-card <?php echo $test_results['database_connection'] ? 'ok' : 'warn'; ?>">
                <div class="summary-value"><?php echo $test_results['database_connection'] ? '✓' : '✗'; ?></div>
                <div class="summary-label">Database</div>
            </div>
            <div class="summary-card <?php echo ($test_results['toppings_table'] && $test_results['toppings_data'] > 0) ? 'ok' : 'warn'; ?>">
                <div class="summary-value"><?php echo $test_results['toppings_data']; ?></div>
                <div class="summary-label">Toppings</div>
            </div>
            <div class="summary-card <?php echo ($test_results['menu_table'] && $test_results['menu_data'] > 0) ? 'ok' : 'warn'; ?>">
                <div class="summary-value"><?php echo $test_results['menu_data']; ?></div>
                <div class="summary-label">Menu Items</div>
            </div>
            <div class="summary-card <?php echo $test_results['upload_dir'] ? 'ok' : 'warn'; ?>">
                <div class="summary-value"><?php echo $test_results['upload_dir'] ? '✓' : '✗'; ?></div>
                <div class="summary-label">Upload Dir</div>
            </div>
        </div>

        <div class="test-group">
            <h3 style="margin-bottom: 12px; color: #333; font-size: 18px;">📊 Hasil Test</h3>
            
            <div class="test-item <?php echo $test_results['database_connection'] ? 'pass' : 'fail'; ?>">
                <span class="status-icon"><?php echo $test_results['database_connection'] ? '✅' : '❌'; ?></span>
                <div class="test-label">Database Connection</div>
                <div class="test-value"><?php echo $test_results['database_connection'] ? 'Connected' : 'Failed'; ?></div>
            </div>

            <div class="test-item <?php echo $test_results['toppings_table'] ? 'pass' : 'fail'; ?>">
                <span class="status-icon"><?php echo $test_results['toppings_table'] ? '✅' : '❌'; ?></span>
                <div class="test-label">Toppings Table</div>
                <div class="test-value"><?php echo $test_results['toppings_table'] ? 'Exists' : 'Not Found'; ?></div>
            </div>

            <div class="test-item info">
                <span class="status-icon">📦</span>
                <div class="test-label">Toppings Data</div>
                <div class="test-value"><?php echo $test_results['toppings_data']; ?> items</div>
            </div>

            <div class="test-item <?php echo $test_results['menu_table'] ? 'pass' : 'fail'; ?>">
                <span class="status-icon"><?php echo $test_results['menu_table'] ? '✅' : '❌'; ?></span>
                <div class="test-label">Menu Table</div>
                <div class="test-value"><?php echo $test_results['menu_table'] ? 'Exists' : 'Not Found'; ?></div>
            </div>

            <div class="test-item info">
                <span class="status-icon">🍹</span>
                <div class="test-label">Menu Items Data</div>
                <div class="test-value"><?php echo $test_results['menu_data']; ?> items</div>
            </div>

            <div class="test-item <?php echo $test_results['upload_dir'] ? 'pass' : 'fail'; ?>">
                <span class="status-icon"><?php echo $test_results['upload_dir'] ? '✅' : '❌'; ?></span>
                <div class="test-label">Upload Directory</div>
                <div class="test-value"><?php echo $test_results['upload_dir'] ? 'Ready' : 'Not Ready'; ?></div>
            </div>

            <div class="test-item <?php echo $test_results['admin_session'] ? 'pass' : 'info'; ?>">
                <span class="status-icon"><?php echo $test_results['admin_session'] ? '✅' : '❓'; ?></span>
                <div class="test-label">Admin Session</div>
                <div class="test-value"><?php echo $test_results['admin_session'] ? 'Logged In' : 'Not Logged In'; ?></div>
            </div>
        </div>

        <?php if (!empty($test_results['errors'])): ?>
            <div class="error-list">
                <strong>⚠️ Warnings/Errors:</strong>
                <ul>
                    <?php foreach ($test_results['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div style="background: #e8f5e9; border-left: 4px solid #4CAF50; padding: 12px; border-radius: 6px; color: #2e7d32; font-weight: 600;">
                ✅ Semua test passed! Sistem menu integration siap digunakan.
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="admin_panel/manage_menu.php" class="btn btn-primary">🔧 Kelola Menu</a>
            <a href="menu.php" class="btn btn-secondary">🍽️ Lihat Menu User</a>
        </div>
    </div>
</body>
</html>

<?php if ($conn) $conn->close(); ?>
