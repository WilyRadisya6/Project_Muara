<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

$page_title = 'Scan QR Code';
include '../includes/config.php';
include '../db_connect.php';

$order = null;
$error = '';
$success = '';

// Handle QR code scan result
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qr_data'])) {
    $qr_data = json_decode($_POST['qr_data'], true);
    
    if ($qr_data && isset($qr_data['order_id'])) {
        $order_id = intval($qr_data['order_id']);
        
        // Get order data
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $order = $row;
            header('Location: order_detail.php?order_id=' . $order_id);
            exit();
        } else {
            $error = 'Pesanan tidak ditemukan!';
        }
        $stmt->close();
    } else {
        $error = 'QR Code tidak valid!';
    }
}

// Handle direct order_id access
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $order = $row;
        header('Location: order_detail.php?order_id=' . $order_id);
        exit();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - Yummy Yamy Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" crossorigin="anonymous" media="all">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body>

<style>
    .scan-section {
        background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%);
        min-height: calc(100vh - 80px - 200px);
        padding: 60px 20px;
    }
    
    .scan-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .scan-header {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        text-align: center;
    }
    
    .scan-header h1 {
        font-size: 42px;
        color: #FF6B35;
        margin: 0 0 10px 0;
        font-family: 'Playfair Display', serif;
    }
    
    .scan-wrapper {
        background: white;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        text-align: center;
    }
    
    #reader {
        width: 100%;
        max-width: 500px;
        margin: 0 auto 20px;
    }
    
    .manual-input {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #FFF5F0;
    }
    
    .manual-input input {
        width: 100%;
        max-width: 400px;
        padding: 12px;
        border: 2px solid rgba(0,0,0,0.1);
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 15px;
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
    
    .back-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 24px;
        background: white;
        color: #FF6B35;
        border: 2px solid #FF6B35;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        background: #FF6B35;
        color: white;
        text-decoration: none;
    }
</style>

<section class="scan-section">
    <div class="scan-container">
        <div class="scan-header">
            <h1>Scan QR Code Pesanan</h1>
            <p style="color: #666; margin: 0;">Arahkan kamera ke QR Code pesanan pelanggan</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="scan-wrapper">
            <div id="reader"></div>
            
            <div class="manual-input">
                <h3 style="margin-bottom: 15px;">Atau Masukkan Order ID Manual</h3>
                <form method="GET" action="order_detail.php">
                    <input type="number" name="order_id" placeholder="Masukkan Order ID" required>
                    <button type="submit" class="primary-btn" style="width: 100%; max-width: 400px; padding: 12px;">Cari Pesanan</button>
                </form>
            </div>
            
            <a href="dashboard.php" class="back-btn">Kembali ke Dashboard</a>
        </div>
    </div>
</section>

<script>
let html5QrcodeScanner = null;

function onScanSuccess(decodedText, decodedResult) {
    // Stop scanning
    html5QrcodeScanner.clear();
    
    // Parse QR data
    try {
        const qrData = JSON.parse(decodedText);
        
        // Submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'scan_qr.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'qr_data';
        input.value = decodedText;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    } catch (e) {
        alert('QR Code tidak valid!');
        // Restart scanner
        startScanner();
    }
}

function onScanFailure(error) {
    // Ignore scan failures
}

function startScanner() {
    html5QrcodeScanner = new Html5Qrcode("reader");
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        onScanSuccess,
        onScanFailure
    );
}

// Start scanner on page load
document.addEventListener('DOMContentLoaded', function() {
    startScanner();
});
</script>

</body>
</html>

