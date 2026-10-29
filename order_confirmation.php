<?php
session_start();

// Set page title
$page_title = 'Konfirmasi Pesanan';

// Sertakan koneksi database
@include 'db_connect.php';

// Sertakan header template
include 'includes/header.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    header('Location: menu.php');
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
    header('Location: menu.php');
    exit();
}

// Generate QR code using API
$qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($order['qr_code']);
?>

<section class="confirmation-section" style="background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%); min-height: calc(100vh - 80px - 200px); padding: 60px 20px;">
    <div class="confirmation-container" style="max-width: 800px; margin: 0 auto;">
        <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center;">
            <h1 style="font-family: 'Playfair Display', serif; color: #FF6B35; margin-bottom: 20px;">Pesanan Berhasil!</h1>
            <p style="font-size: 18px; margin-bottom: 30px; color: #666;">Nomor Pesanan: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong></p>
            
            <!-- QR Code -->
            <div style="margin: 30px 0;">
                <h3 style="margin-bottom: 20px;">QR Code Pesanan</h3>
                <img src="<?php echo $qr_code_url; ?>" alt="QR Code" style="border: 4px solid #FF6B35; border-radius: 12px; padding: 10px; background: white;">
                <p style="margin-top: 15px; color: #666; font-size: 14px;">Tunjukkan QR Code ini saat mengambil pesanan</p>
            </div>
            
            <!-- Order Details -->
            <div style="text-align: left; margin-top: 40px; padding-top: 30px; border-top: 2px solid #FFF5F0;">
                <h3 style="margin-bottom: 20px; color: #333;">Detail Pesanan</h3>
                
                <div style="margin-bottom: 15px;">
                    <strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Telepon:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?>
                </div>
                <?php if ($order['customer_email']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?>
                </div>
                <?php endif; ?>
                <div style="margin-bottom: 15px;">
                    <strong>Metode Pembayaran:</strong> 
                    <?php 
                    $payment_methods = [
                        'cash' => 'Tunai',
                        'qris' => 'QRIS',
                        'bank_transfer' => 'Transfer Bank',
                        'e_wallet' => 'E-Wallet'
                    ];
                    echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                    ?>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Status:</strong> 
                    <span style="padding: 4px 12px; background: #FFF5F0; color: #FF6B35; border-radius: 20px; font-size: 12px;">
                        <?php echo ucfirst($order['order_status']); ?>
                    </span>
                </div>
                
                <!-- Order Items by Portion -->
                <?php
                $current_portion = 0;
                foreach ($order_items as $item):
                    if ($item['portion_number'] != $current_portion):
                        if ($current_portion > 0) echo '</div>';
                        $current_portion = $item['portion_number'];
                ?>
                    <div style="margin-top: 20px; padding: 15px; background: #FFF5F0; border-radius: 8px;">
                        <h4 style="color: #FF6B35; margin-bottom: 10px;">Porsi <?php echo $current_portion; ?></h4>
                <?php endif; ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span><?php echo htmlspecialchars($item['topping_name']); ?> x<?php echo $item['quantity']; ?></span>
                            <span>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 10px;">
                            Kuah: <?php echo htmlspecialchars($item['kuah']); ?>
                        </div>
                <?php endforeach; ?>
                <?php if ($current_portion > 0) echo '</div>'; ?>
                
                <!-- Drinks -->
                <?php if (!empty($order_drinks)): ?>
                <div style="margin-top: 20px; padding: 15px; background: #FFF5F0; border-radius: 8px;">
                    <h4 style="color: #FF6B35; margin-bottom: 10px;">Minuman</h4>
                    <?php foreach ($order_drinks as $drink): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span><?php echo htmlspecialchars($drink['drink_name']); ?> x<?php echo $drink['quantity']; ?></span>
                            <span>Rp <?php echo number_format($drink['subtotal'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #FF6B35; display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: #FF6B35;">
                    <span>Total:</span>
                    <span>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <div style="margin-top: 40px;">
                <a href="menu.php" class="primary-btn" style="display: inline-block; padding: 12px 24px; text-decoration: none;">Kembali ke Menu</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

