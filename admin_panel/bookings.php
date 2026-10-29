<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$page_title = 'Table Bookings';
include '../db_connect.php';

$bookings = [];
$error = '';
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $booking_id = intval($_POST['booking_id']);
    $new_status = htmlspecialchars($_POST['status']);
    
    if ($conn && !$conn->connect_error) {
        $stmt = $conn->prepare("UPDATE table_bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $booking_id);
        
        if ($stmt->execute()) {
            $success = 'Status booking berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui status booking.';
        }
        $stmt->close();
    }
}

// Fetch bookings
if ($conn && !$conn->connect_error) {
    $result = $conn->query("SELECT * FROM table_bookings ORDER BY booking_date DESC, booking_time DESC, created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Bookings - Yummy Yamy Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
    <style>
        body {
            background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
            min-height: 100vh;
        }
        
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
        
        .bookings-section {
            padding: 40px 20px;
        }
        
        .bookings-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .bookings-header {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C5A 100%);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
            margin-bottom: 30px;
            color: white;
        }
        
        .bookings-header h1 {
            font-size: 48px;
            color: white;
            margin: 0 0 10px 0;
            font-family: 'Playfair Display', serif;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .bookings-header h1::before {
            content: '📅';
            font-size: 48px;
        }
        
        .bookings-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .bookings-table thead th {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C5A 100%);
            color: white;
            padding: 18px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .bookings-table tbody td {
            padding: 16px;
            border-bottom: 1px solid #eee;
        }
        
        .bookings-table tbody tr:hover {
            background: #FFF5F0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4CAF50;
        }
    </style>
</head>
<body>

<!-- Admin Navbar -->
<nav class="admin-navbar">
    <div class="admin-navbar-left">
        <a href="dashboard.php" class="admin-navbar-logo">
            <img src="../assets/logoyummy.png" alt="Yummy Yamy Logo">
            <span>Yummy Yamy Admin</span>
        </a>
    </div>
    <div class="admin-navbar-right">
        <a href="dashboard.php" class="admin-navbar-link">Dashboard</a>
        <a href="manage_menu.php" class="admin-navbar-link">Manage Menu</a>
        <a href="bookings.php" class="admin-navbar-link active">Bookings</a>
        <a href="scan_qr.php" class="admin-navbar-link">Scan QR</a>
        <a href="logout.php" class="admin-navbar-link" style="background: #FF6B35; color: white;">Logout</a>
    </div>
</nav>

<section class="bookings-section">
    <div class="bookings-container">
        <div class="bookings-header">
            <h1>Table Bookings</h1>
            <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 16px;">Kelola reservasi meja pelanggan</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="bookings-card">
            <?php if (empty($bookings)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 64px; margin-bottom: 20px;">📅</div>
                    <p style="color: #999; font-size: 16px; margin: 0;">Belum ada booking meja.</p>
                </div>
            <?php else: ?>
                <div style="background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%); padding: 15px 20px; border-radius: 12px; margin-bottom: 25px;">
                    <span style="color: #666; font-weight: 600;">Total: <strong style="color: #FF6B35;"><?php echo count($bookings); ?></strong> Booking</span>
                </div>
                
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Jumlah Orang</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['customer_email'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($booking['customer_phone']); ?></td>
                                <td><?php echo $booking['number_of_people']; ?> orang</td>
                                <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                <td><?php echo date('H:i', strtotime($booking['booking_time'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #ddd; font-size: 12px;">
                                            <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            <?php if (!empty($booking['special_requests'])): ?>
                                <tr>
                                    <td colspan="9" style="background: #f9f9f9; padding-left: 40px; font-size: 13px; color: #666;">
                                        <strong>Permintaan Khusus:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="dashboard.php" class="secondary-btn">← Kembali ke Dashboard</a>
        </div>
    </div>
</section>

</body>
</html>

<?php
if ($conn) {
    $conn->close();
}
?>

