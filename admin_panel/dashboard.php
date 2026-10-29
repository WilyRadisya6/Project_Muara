<?php 
    session_start();
    
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        header('Location: login.php');
        exit();
    }
    
    $page_title = 'Admin Dashboard';
    include '../includes/config.php';
    include '../includes/database.php';
    
    $admin = new Admin($conn);
    $total_users = $admin->getUserCount();
    $active_users = $admin->getActiveUserCount();
    $all_users = $admin->getAllUsers();
    
    // Get topping/menu statistics
    $total_toppings = 0;
    $low_stock_toppings = 0;
    $total_menu_items = 0;
    $total_bookings = 0;
    $pending_bookings = 0;
    if ($conn && !$conn->connect_error) {
        $table_check = $conn->query("SHOW TABLES LIKE 'toppings'");
        if ($table_check && $table_check->num_rows > 0) {
            $result = $conn->query("SELECT COUNT(*) as total FROM toppings");
            if ($result) {
                $row = $result->fetch_assoc();
                $total_toppings = intval($row['total']);
            }
            
            $low_stock_result = $conn->query("SELECT COUNT(*) as total FROM toppings WHERE stock < 10");
            if ($low_stock_result) {
                $low_row = $low_stock_result->fetch_assoc();
                $low_stock_toppings = intval($low_row['total']);
            }
        }
        // Count items in `menu` table (minuman & lainnya) and add to total_menu_items
        $menu_check = $conn->query("SHOW TABLES LIKE 'menu'");
        if ($menu_check && $menu_check->num_rows > 0) {
            $menu_res = $conn->query("SELECT COUNT(*) as total FROM menu");
            if ($menu_res) {
                $menu_row = $menu_res->fetch_assoc();
                $total_menu_items = intval($menu_row['total']);
            }
        }
        // aggregate total items (toppings + menu)
        $total_menu_items = $total_menu_items + $total_toppings;
        
        // Get booking statistics
        $booking_check = $conn->query("SHOW TABLES LIKE 'table_bookings'");
        if ($booking_check && $booking_check->num_rows > 0) {
            $booking_result = $conn->query("SELECT COUNT(*) as total FROM table_bookings");
            if ($booking_result) {
                $booking_row = $booking_result->fetch_assoc();
                $total_bookings = intval($booking_row['total']);
            }
            
            $pending_result = $conn->query("SELECT COUNT(*) as total FROM table_bookings WHERE status = 'pending'");
            if ($pending_result) {
                $pending_row = $pending_result->fetch_assoc();
                $pending_bookings = intval($pending_row['total']);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPageTitle($page_title); ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
    <style>
        body {
            background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>

<style>
    .admin-navbar {
        background: white;
        padding: 20px 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .admin-navbar-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .admin-navbar-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
    }
    
    .admin-navbar-logo img {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }
    
    .admin-navbar-logo span {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        font-weight: 600;
        color: #FF6B35;
    }
    
    .admin-navbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .admin-navbar-link {
        padding: 8px 16px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .admin-navbar-link:hover {
        background: #FFF5F0;
        color: #FF6B35;
    }
    
    .admin-navbar-link.active {
        background: #FF6B35;
        color: white;
    }
    
    .admin-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 100px);
        padding: 40px 20px;
    }
    
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 50px;
        padding: 40px;
        background: linear-gradient(135deg, #FF6B35 0%, #FF8C5A 100%);
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .admin-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .admin-header-left {
        position: relative;
        z-index: 1;
    }
    
    .admin-header-left h1 {
        font-size: 48px;
        color: white;
        margin: 0 0 8px 0;
        font-family: 'Playfair Display', serif;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .admin-header-left h1::before {
        content: '👋';
        font-size: 48px;
    }
    
    .admin-header-left p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-size: 16px;
    }
    
    .admin-header-right {
        position: relative;
        z-index: 1;
    }
    
    .admin-header-right .primary-btn {
        padding: 12px 24px;
        font-size: 14px;
    }
    
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: white;
        padding: 32px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-left: 5px solid #FF6B35;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #FF6B35 0%, #FF8C5A 100%);
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(255, 107, 53, 0.2);
    }
    
    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .stat-card h3 {
        margin: 0;
        color: #666;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        background: #FFF5F0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .stat-value {
        font-size: 42px;
        font-weight: 700;
        color: #FF6B35;
        font-family: 'Inter', sans-serif;
    }
    
    .quick-actions-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 40px;
        margin-bottom: 40px;
    }
    
    .quick-actions-section h2 {
        margin-top: 0;
        color: #333;
        border-bottom: 2px solid #FFF5F0;
        padding-bottom: 20px;
        margin-bottom: 30px;
        font-size: 28px;
        font-family: 'Playfair Display', serif;
    }
    
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
    }
    
    .action-card {
        text-align: center;
        padding: 40px 30px;
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        border-radius: 20px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .action-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.05) 0%, transparent 70%);
        transition: all 0.5s ease;
    }
    
    .action-card:hover::before {
        top: -30%;
        right: -30%;
    }
    
    .action-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: #FF6B35;
        box-shadow: 0 12px 35px rgba(255, 107, 53, 0.3);
        background: linear-gradient(135deg, #fff 0%, #FFF5F0 100%);
    }
    
    .action-card-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #FF6B35 0%, #FF8C5A 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        color: white;
        box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    .action-card:hover .action-card-icon {
        transform: rotate(5deg) scale(1.1);
        box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    }
    
    .action-card h3 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 20px;
        font-weight: 600;
    }
    
    .action-card p {
        margin: 0 0 20px 0;
        color: #666;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .action-card .primary-btn {
        width: 100%;
        padding: 12px;
        font-size: 14px;
    }
    
    .users-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 40px;
        overflow-x: auto;
    }
    
    .users-section h2 {
        margin-top: 0;
        color: #333;
        border-bottom: 2px solid #FFF5F0;
        padding-bottom: 20px;
        margin-bottom: 30px;
        font-size: 28px;
        font-family: 'Playfair Display', serif;
    }
    
    .users-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .users-table thead th {
        background: #FF6B35;
        color: white;
        padding: 16px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .users-table tbody td {
        padding: 16px 12px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        color: #333;
    }
    
    .users-table tbody tr:hover {
        background: #FFF5F0;
    }
    
    .users-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-active {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .status-inactive {
        background: #ffebee;
        color: #c62828;
    }
    
    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .btn-small {
        padding: 6px 14px;
        font-size: 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .btn-verify {
        background: #4CAF50;
        color: white;
    }
    
    .btn-verify:hover {
        background: #45a049;
        transform: translateY(-2px);
    }
    
    .btn-reset {
        background: #2196F3;
        color: white;
    }
    
    .btn-reset:hover {
        background: #0b7dda;
        transform: translateY(-2px);
    }
    
    .btn-deactivate {
        background: #FF9800;
        color: white;
    }
    
    .btn-deactivate:hover {
        background: #e68900;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: #f44336;
        color: white;
    }
    
    .btn-delete:hover {
        background: #da190b;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        
        .admin-stats {
            grid-template-columns: 1fr;
        }
        
        .quick-actions-grid {
            grid-template-columns: 1fr;
        }
        
        .users-table {
            font-size: 12px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-small {
            width: 100%;
            text-align: center;
        }
    }
</style>

<!-- Admin Navbar -->
<nav class="admin-navbar">
    <div class="admin-navbar-left">
        <a href="dashboard.php" class="admin-navbar-logo">
            <img src="../assets/logoyummy.png" alt="Yummy Yamy Logo">
            <span>Yummy Yamy Admin</span>
        </a>
    </div>
    <div class="admin-navbar-right">
        <a href="dashboard.php" class="admin-navbar-link active">Dashboard</a>
        <a href="manage_menu.php" class="admin-navbar-link">Manage Menu</a>
        <a href="scan_qr.php" class="admin-navbar-link">Scan QR</a>
        <a href="logout.php" class="admin-navbar-link" style="background: #FF6B35; color: white;">Logout</a>
    </div>
</nav>

<section class="admin-section">
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <h1>Admin Dashboard</h1>
                <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></p>
            </div>
            <div class="admin-header-right">
                <a href="logout.php" class="primary-btn" style="background: rgba(255,255,255,0.2); border: 2px solid white;">Logout</a>
            </div>
        </div>
        
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Total Users</h3>
                    <div class="stat-icon">👥</div>
                </div>
                <div class="stat-value"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Active Users</h3>
                    <div class="stat-icon">✓</div>
                </div>
                <div class="stat-value"><?php echo $active_users; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Total Menu Items</h3>
                    <div class="stat-icon">🍽️</div>
                </div>
                <div class="stat-value"><?php echo $total_menu_items; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Low Stock Alert</h3>
                    <div class="stat-icon">⚠</div>
                </div>
                <div class="stat-value" style="color: <?php echo $low_stock_toppings > 0 ? '#f44336' : '#FF6B35'; ?>;"><?php echo $low_stock_toppings; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Total Bookings</h3>
                    <div class="stat-icon">📅</div>
                </div>
                <div class="stat-value"><?php echo $total_bookings; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Pending Bookings</h3>
                    <div class="stat-icon">⏳</div>
                </div>
                <div class="stat-value" style="color: <?php echo $pending_bookings > 0 ? '#FF9800' : '#FF6B35'; ?>;"><?php echo $pending_bookings; ?></div>
            </div>
        </div>
        
        <div class="quick-actions-section">
            <h2>Quick Actions</h2>
            <div class="quick-actions-grid">
                <a href="manage_menu.php" class="action-card" style="text-decoration: none; color: inherit;">
                    <div class="action-card-icon">🍽️</div>
                    <h3>Manage Menu</h3>
                    <p>Tambah, edit, dan update topping serta minuman/menu lainnya</p>
                    <span class="primary-btn" style="display: inline-block;">Kelola Menu</span>
                </a>
                <a href="scan_qr.php" class="action-card" style="text-decoration: none; color: inherit;">
                    <div class="action-card-icon">📱</div>
                    <h3>Scan QR Code</h3>
                    <p>Scan QR code pesanan pelanggan untuk melihat detail pesanan</p>
                    <span class="prima
                    ry-btn" style="display: inline-block;">Scan QR Code</span>
                </a>
            </div>
        </div>
        
        <div class="users-section">
            <h2>Manage Users</h2>
            
            <?php if (count($all_users) > 0): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $user['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if (!$user['is_active']): ?>
                                            <a href="verify_user.php?id=<?php echo $user['id']; ?>" class="btn-small btn-verify">Verify</a>
                                        <?php else: ?>
                                            <a href="deactivate_user.php?id=<?php echo $user['id']; ?>" class="btn-small btn-deactivate">Deactivate</a>
                                        <?php endif; ?>
                                        <a href="reset_password.php?id=<?php echo $user['id']; ?>" class="btn-small btn-reset">Reset Password</a>
                                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">No users registered yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>

