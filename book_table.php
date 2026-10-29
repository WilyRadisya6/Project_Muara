<?php
session_start();
$page_title = 'Book Table';
include 'includes/header.php';
include 'db_connect.php';

$error = '';
$success = '';

// Create bookings table if not exists
if ($conn && !$conn->connect_error) {
    $create_table = "CREATE TABLE IF NOT EXISTS table_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(120),
        customer_phone VARCHAR(20) NOT NULL,
        number_of_people INT NOT NULL,
        booking_date DATE NOT NULL,
        booking_time TIME NOT NULL,
        special_requests TEXT,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_booking_date (booking_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->query($create_table);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $number_of_people = intval($_POST['number_of_people'] ?? 0);
    $booking_date = trim($_POST['booking_date'] ?? '');
    $booking_time = trim($_POST['booking_time'] ?? '');
    $special_requests = trim($_POST['special_requests'] ?? '');
    
    if (empty($customer_name) || empty($customer_phone) || empty($number_of_people) || empty($booking_date) || empty($booking_time)) {
        $error = 'Mohon lengkapi semua field yang wajib diisi!';
    } elseif ($number_of_people < 1) {
        $error = 'Jumlah orang minimal 1 orang!';
    } else {
        if ($conn && !$conn->connect_error) {
            $stmt = $conn->prepare("INSERT INTO table_bookings (customer_name, customer_email, customer_phone, number_of_people, booking_date, booking_time, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisss", $customer_name, $customer_email, $customer_phone, $number_of_people, $booking_date, $booking_time, $special_requests);
            
            if ($stmt->execute()) {
                $success = 'Booking meja berhasil! Kami akan menghubungi Anda untuk konfirmasi.';
                // Clear form
                $_POST = [];
            } else {
                $error = 'Gagal menyimpan booking. Silakan coba lagi.';
            }
            $stmt->close();
        } else {
            $error = 'Koneksi database gagal.';
        }
    }
}
?>

<section class="order-form-section" style="padding: 80px 20px; background: linear-gradient(135deg, #FFF5F0 0%, #fff 100%); min-height: calc(100vh - 200px);">
    <div class="order-form-container" style="max-width: 800px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 48px; color: #FF6B35; margin: 0 0 15px 0;">
                🍽️ Book Table
            </h1>
            <p style="color: #666; font-size: 18px; margin: 0;">Reservasi meja untuk pengalaman makan yang lebih baik</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 30px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: 30px;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="admin-card" style="padding: 40px; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
            <form method="POST" class="seblak-order-form">
                <div class="form-group">
                    <label for="customer_name">Nama Lengkap *</label>
                    <input type="text" id="customer_name" name="customer_name" 
                           value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>" 
                           placeholder="Masukkan nama lengkap Anda" required>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="customer_email">Email</label>
                        <input type="email" id="customer_email" name="customer_email" 
                               value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>" 
                               placeholder="email@example.com">
                    </div>

                    <div class="form-group">
                        <label for="customer_phone">Nomor Telepon *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" 
                               value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>" 
                               placeholder="081234567890" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="number_of_people">Jumlah Orang *</label>
                    <input type="number" id="number_of_people" name="number_of_people" 
                           value="<?php echo isset($_POST['number_of_people']) ? htmlspecialchars($_POST['number_of_people']) : ''; ?>" 
                           min="1" max="20" placeholder="1" required>
                    <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Maksimal 20 orang per meja</small>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="booking_date">Tanggal Booking *</label>
                        <input type="date" id="booking_date" name="booking_date" 
                               value="<?php echo isset($_POST['booking_date']) ? htmlspecialchars($_POST['booking_date']) : ''; ?>" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="booking_time">Waktu Booking *</label>
                        <input type="time" id="booking_time" name="booking_time" 
                               value="<?php echo isset($_POST['booking_time']) ? htmlspecialchars($_POST['booking_time']) : ''; ?>" 
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="special_requests">Permintaan Khusus (Opsional)</label>
                    <textarea id="special_requests" name="special_requests" rows="4" 
                              placeholder="Contoh: Meja di dekat jendela, perayaan ulang tahun, dll."><?php echo isset($_POST['special_requests']) ? htmlspecialchars($_POST['special_requests']) : ''; ?></textarea>
                </div>

                <button type="submit" class="add-to-cart-btn" style="width: 100%; padding: 18px; font-size: 18px; font-weight: 600; margin-top: 20px;">
                    📅 Book Table Sekarang
                </button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="secondary-btn">← Kembali ke Beranda</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

