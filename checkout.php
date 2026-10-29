<?php
session_start();

// Set page title
$page_title = 'Checkout';

// Sertakan koneksi database
@include 'db_connect.php';

// Setup tabel jika belum ada (hanya sekali, tidak setiap kali load)
if ($conn && !$conn->connect_error) {
    // Check if tables exist, if not create them
    $tables_check = $conn->query("SHOW TABLES LIKE 'orders'");
    if (!$tables_check || $tables_check->num_rows == 0) {
        // Include setup only if tables don't exist
        @include 'setup_orders_table.php';
    }
}

// Sertakan header template
include 'includes/header.php';

// Ambil data dari session cart
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$portions = isset($_SESSION['portions']) ? $_SESSION['portions'] : [];
$drinks = isset($_SESSION['drinks']) ? $_SESSION['drinks'] : [];

if (empty($portions)) {
    header('Location: menu.php');
    exit();
}

// Metode pembayaran
$payment_methods = [
    'cash' => 'Tunai',
    'qris' => 'QRIS',
    'bank_transfer' => 'Transfer Bank',
    'e_wallet' => 'E-Wallet (GoPay, OVO, DANA)'
];

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');
    
    if (empty($customer_name) || empty($customer_phone) || empty($payment_method)) {
        $error = 'Mohon lengkapi semua field yang wajib diisi!';
    } else {
        // Generate order number
        $order_number = 'YY' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        
        // Calculate total
        $total_price = 0;
        foreach ($portions as $portion) {
            $total_price += floatval($portion['total']);
        }
        
        // Add drinks total
        foreach ($drinks as $drink) {
            $total_price += floatval($drink['total']);
        }
        
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (order_number, customer_name, customer_phone, customer_email, total_price, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, ?, 'pending', 'pending')");
        $stmt->bind_param("ssssds", $order_number, $customer_name, $customer_phone, $customer_email, $total_price, $payment_method);
        
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            
                // Insert order items (portions) — pedas removed
            foreach ($portions as $portion_num => $portion) {
                $portion_number = $portion_num + 1;
                
                // Insert toppings for this portion
                foreach ($portion['toppings'] as $topping) {
                    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, portion_number, topping_id, topping_name, topping_price, quantity, kuah, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $topping_id = $topping['id'] ?? null;
                    $topping_name = $topping['name'];
                    $topping_price = floatval($topping['price']);
                    $quantity = intval($topping['quantity']);
                    $kuah = $portion['kuah'];
                    $subtotal = $topping_price * $quantity;
                    
                    // Handle null topping_id
                    if ($topping_id === null) {
                        $topping_id = 0; // Set to 0 if null
                    }
                    
                    // Bind params: order_id(i), portion_number(i), topping_id(i), topping_name(s), topping_price(d), quantity(i), kuah(s), subtotal(d)
                    $stmt_item->bind_param("iiisdisd", $order_id, $portion_number, $topping_id, $topping_name, $topping_price, $quantity, $kuah, $subtotal);
                    $stmt_item->execute();
                    $stmt_item->close();
                }
            }
            
            // Insert drinks if any
            if (!empty($drinks)) {
                foreach ($drinks as $drink) {
                    $stmt_drink = $conn->prepare("INSERT INTO order_drinks (order_id, drink_name, drink_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
                    $drink_name = $drink['name'];
                    $drink_price = floatval($drink['price']);
                    $drink_quantity = intval($drink['quantity']);
                    $drink_subtotal = floatval($drink['total']);
                    
                    $stmt_drink->bind_param("isdid", $order_id, $drink_name, $drink_price, $drink_quantity, $drink_subtotal);
                    $stmt_drink->execute();
                    $stmt_drink->close();
                }
            }
            
            // Generate QR code data
            $qr_data = json_encode([
                'order_id' => $order_id,
                'order_number' => $order_number,
                'total' => $total_price
            ]);
            
            // Update order with QR code
            $stmt_qr = $conn->prepare("UPDATE orders SET qr_code = ? WHERE id = ?");
            $stmt_qr->bind_param("si", $qr_data, $order_id);
            $stmt_qr->execute();
            $stmt_qr->close();
            
            $stmt->close();
            
            // Clear cart
            unset($_SESSION['cart']);
            unset($_SESSION['portions']);
            unset($_SESSION['drinks']);
            
            // Redirect to order confirmation
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit();
        } else {
            $error = 'Gagal membuat pesanan: ' . $stmt->error;
        }
    }
}
?>

<section class="checkout-section" style="background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%); min-height: calc(100vh - 80px - 200px); padding: 60px 20px;">
    <div class="checkout-container" style="max-width: 1200px; margin: 0 auto;">
        <h1 style="font-family: 'Playfair Display', serif; color: #FF6B35; margin-bottom: 30px;">Checkout</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="padding: 16px; background: #ffebee; color: #c62828; border-left: 4px solid #f44336; border-radius: 8px; margin-bottom: 24px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Form Checkout -->
            <div class="checkout-form-wrapper" style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                <h2 style="font-family: 'Playfair Display', serif; margin-bottom: 30px;">Data Pelanggan</h2>
                
                <form method="POST" action="">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nama Lengkap *</label>
                        <input type="text" name="customer_name" required style="width: 100%; padding: 12px; border: 2px solid rgba(0,0,0,0.1); border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">No. Telepon *</label>
                        <input type="tel" name="customer_phone" required style="width: 100%; padding: 12px; border: 2px solid rgba(0,0,0,0.1); border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Email (Opsional)</label>
                        <input type="email" name="customer_email" style="width: 100%; padding: 12px; border: 2px solid rgba(0,0,0,0.1); border-radius: 8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Metode Pembayaran *</label>
                        <select name="payment_method" required style="width: 100%; padding: 12px; border: 2px solid rgba(0,0,0,0.1); border-radius: 8px;">
                            <option value="">Pilih Metode Pembayaran</option>
                            <?php foreach ($payment_methods as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="place_order" class="primary-btn" style="width: 100%; padding: 16px; font-size: 16px;">Buat Pesanan & Generate QR Code</button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary" style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); height: fit-content;">
                <h2 style="font-family: 'Playfair Display', serif; margin-bottom: 30px;">Ringkasan Pesanan</h2>
                
                <?php 
                $grand_total = 0;
                $portions_total = 0;
                $drinks_total = 0;
                
                // Calculate portions total
                foreach ($portions as $idx => $portion): 
                    $portions_total += floatval($portion['total']);
                ?>
                    <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                        <h3 style="color: #FF6B35; margin-bottom: 15px;">Porsi <?php echo $idx + 1; ?></h3>
                        <div style="margin-bottom: 10px;">
                            <strong>Topping:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <?php foreach ($portion['toppings'] as $topping): ?>
                                    <li><?php echo htmlspecialchars($topping['name']); ?> x<?php echo $topping['quantity']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Kuah:</strong> <?php echo htmlspecialchars($portion['kuah']); ?>
                        </div>
                        <!-- Level Pedas removed -->
                        <div style="text-align: right; font-weight: 600; color: #FF6B35;">
                            Rp <?php echo number_format($portion['total'], 0, ',', '.'); ?>
                        </div>
                    </div>
                <?php endforeach; 
                
                // Calculate drinks total
                if (!empty($drinks)) {
                    foreach ($drinks as $drink) {
                        $drinks_total += floatval($drink['total']);
                    }
                }
                
                $grand_total = $portions_total + $drinks_total;
                ?>
                
                <?php if (!empty($drinks)): ?>
                    <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                        <h3 style="color: #FF6B35; margin-bottom: 15px;">🍹 Minuman</h3>
                        <?php foreach ($drinks as $drink): 
                            $drink_total = floatval($drink['total']);
                        ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                <span><strong><?php echo htmlspecialchars($drink['name']); ?></strong> x<?php echo $drink['quantity']; ?></span>
                                <span style="color: #FF6B35; font-weight: 600;">Rp <?php echo number_format($drink_total, 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: right; margin-top: 10px; font-weight: 600; color: #666;">
                            Subtotal Minuman: <span style="color: #FF6B35;">Rp <?php echo number_format($drinks_total, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div style="border-top: 2px solid #FF6B35; padding-top: 20px; margin-top: 20px;">
                    <?php if (!empty($drinks)): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px; color: #666;">
                            <span>Total Porsi:</span>
                            <span>Rp <?php echo number_format($portions_total, 0, ',', '.'); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px; color: #666;">
                            <span>Total Minuman:</span>
                            <span>Rp <?php echo number_format($drinks_total, 0, ',', '.'); ?></span>
                        </div>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: #FF6B35; padding-top: 10px; border-top: 2px solid #FF6B35;">
                        <span>Total Keseluruhan:</span>
                        <span>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

