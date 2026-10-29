<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

$page_title = 'Detail Pesanan';
include '../includes/config.php';
include '../db_connect.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    header('Location: scan_qr.php');
    exit();
}

// Get order data
$order = null;
if ($conn && !$conn->connect_error) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $order = $row;
    }
    $stmt->close();
    
    // Get order items
    $order_items = [];
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY portion_number, id");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $order_items[] = $row;
    }
    $stmt->close();
    
    // Get order drinks
    $order_drinks = [];
    $stmt = $conn->prepare("SELECT * FROM order_drinks WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $order_drinks[] = $row;
    }
    $stmt->close();
}

if (!$order) {
    header('Location: scan_qr.php');
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['order_status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    
    header('Location: order_detail.php?order_id=' . $order_id);
    exit();
}

$payment_methods = [
    'cash' => 'Tunai',
    'qris' => 'QRIS',
    'bank_transfer' => 'Transfer Bank',
    'e_wallet' => 'E-Wallet'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Yummy Yamy Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
</head>
<body>

<style>
    .order-detail-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 60px 20px;
    }
    
    .order-detail-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .order-header {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    
    .order-header h1 {
        font-size: 42px;
        color: #FF6B35;
        margin: 0 0 10px 0;
        font-family: 'Playfair Display', serif;
    }
    
    .order-info {
        background: white;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    
    .info-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #666;
    }
    
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-processing {
        background: #cfe2ff;
        color: #084298;
    }
    
    .status-ready {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-completed {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-cancelled {
        background: #f8d7da;
        color: #842029;
    }
    
    .portion-section {
        background: #FFF5F0;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .portion-title {
        color: #FF6B35;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .action-buttons {
        margin-top: 30px;
        display: flex;
        gap: 15px;
    }
</style>

<section class="order-detail-section">
    <div class="order-detail-container">
        <div class="order-header">
            <h1>Detail Pesanan</h1>
            <p style="color: #666; margin: 0;">Nomor Pesanan: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong></p>
        </div>
        
        <div class="order-info">
            <h2 style="margin-top: 0; margin-bottom: 30px; font-family: 'Playfair Display', serif;">Informasi Pesanan</h2>
            
            <div class="info-row">
                <div class="info-label">Nama Pelanggan:</div>
                <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">No. Telepon:</div>
                <div><?php echo htmlspecialchars($order['customer_phone']); ?></div>
            </div>
            
            <?php if ($order['customer_email']): ?>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div><?php echo htmlspecialchars($order['customer_email']); ?></div>
            </div>
            <?php endif; ?>
            
            <div class="info-row">
                <div class="info-label">Metode Pembayaran:</div>
                <div><?php echo $payment_methods[$order['payment_method']] ?? $order['payment_method']; ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status Pesanan:</div>
                <div>
                    <span class="status-badge status-<?php echo $order['order_status']; ?>">
                        <?php echo ucfirst($order['order_status']); ?>
                    </span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tanggal Pesanan:</div>
                <div><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></div>
            </div>
            
            <!-- Update Status Form -->
            <form method="POST" style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #FFF5F0;">
                <div class="info-row">
                    <div class="info-label">Ubah Status:</div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select name="order_status" style="padding: 10px; border: 2px solid rgba(0,0,0,0.1); border-radius: 8px; flex: 1;">
                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="ready" <?php echo $order['order_status'] == 'ready' ? 'selected' : ''; ?>>Ready</option>
                            <option value="completed" <?php echo $order['order_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status" class="primary-btn" style="padding: 10px 20px;">Update</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Order Items by Portion -->
        <div class="order-info">
            <h2 style="margin-top: 0; margin-bottom: 30px; font-family: 'Playfair Display', serif;">Detail Porsi</h2>
            
            <?php
            $current_portion = 0;
            foreach ($order_items as $item):
                if ($item['portion_number'] != $current_portion):
                    if ($current_portion > 0) echo '</div>';
                    $current_portion = $item['portion_number'];
            ?>
                <div class="portion-section">
                    <div class="portion-title">Porsi <?php echo $current_portion; ?></div>
                    <div style="margin-bottom: 10px; color: #666;">
                        <strong>Kuah:</strong> <?php echo htmlspecialchars($item['kuah']); ?>
                    </div>
            <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: white; border-radius: 8px;">
                        <span><?php echo htmlspecialchars($item['topping_name']); ?> x<?php echo $item['quantity']; ?></span>
                        <span style="font-weight: 600; color: #FF6B35;">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
                    </div>
            <?php endforeach; ?>
            <?php if ($current_portion > 0) echo '</div>'; ?>
            
            <!-- Drinks -->
            <?php if (!empty($order_drinks)): ?>
            <div class="portion-section" style="margin-top: 20px;">
                <div class="portion-title">Minuman</div>
                <?php foreach ($order_drinks as $drink): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: white; border-radius: 8px;">
                        <span><?php echo htmlspecialchars($drink['drink_name']); ?> x<?php echo $drink['quantity']; ?></span>
                        <span style="font-weight: 600; color: #FF6B35;">Rp <?php echo number_format($drink['subtotal'], 0, ',', '.'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #FF6B35; display: flex; justify-content: space-between; font-size: 24px; font-weight: 700; color: #FF6B35;">
                <span>Total:</span>
                <span>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="scan_qr.php" class="primary-btn" style="display: inline-block; padding: 12px 24px; text-decoration: none;">Scan QR Code Lagi</a>
            <a href="dashboard.php" class="secondary-btn" style="display: inline-block; padding: 12px 24px; background: white; color: #FF6B35; border: 2px solid #FF6B35; border-radius: 8px; text-decoration: none; font-weight: 600;">Kembali ke Dashboard</a>
        </div>
    </div>
</section>

</body>
</html>

