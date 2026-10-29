<?php 
    // Set page title
    $page_title = 'Menu';
    
    // Sertakan koneksi database
    @include 'db_connect.php';
    
    // Sertakan header template
    include 'includes/header.php'; 

    // Ambil data topping dari database jika ada, jika tidak gunakan data default
    $toppings = [];
    if (isset($conn) && $conn && !isset($conn->connect_error)) {
        $table_check = @$conn->query("SHOW TABLES LIKE 'toppings'");
        if ($table_check && $table_check->num_rows > 0) {
            $result = @$conn->query("SELECT * FROM toppings ORDER BY id");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $toppings[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'price' => floatval($row['price']),
                        'stock' => intval($row['stock']),
                        'image' => $row['image']
                    ];
                }
            }
        }
    }
    
    // Jika tidak ada data di database, gunakan data default
    if (empty($toppings)) {
        $toppings = [
            [
                'id' => 1,
                'name' => 'Telur',
                'price' => 3000,
                'stock' => 50,
                'image' => 'assets/seblak1.jpeg'
            ],
            [
                'id' => 2,
                'name' => 'Bakso',
                'price' => 5000,
                'stock' => 40,
                'image' => 'assets/seblak2.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Sosis',
                'price' => 5000,
                'stock' => 45,
                'image' => 'assets/seblak1.jpeg'
            ],
            [
                'id' => 4,
                'name' => 'Ceker',
                'price' => 8000,
                'stock' => 30,
                'image' => 'assets/Sebalk ceker.jpg'
            ],
            [
                'id' => 5,
                'name' => 'Kerupuk',
                'price' => 2000,
                'stock' => 60,
                'image' => 'assets/seblak2.jpg'
            ],
            [
                'id' => 6,
                'name' => 'Mie',
                'price' => 3000,
                'stock' => 55,
                'image' => 'assets/seblak1.jpeg'
            ],
            [
                'id' => 7,
                'name' => 'Tahu',
                'price' => 2000,
                'stock' => 50,
                'image' => 'assets/seblak2.jpg'
            ],
            [
                'id' => 8,
                'name' => 'Baso Aci',
                'price' => 6000,
                'stock' => 35,
                'image' => 'assets/Recent Work Baso Aci I Visser.jpg'
            ]
        ];
    }
    
    $kuah_options = [
        'Asin' => 0,
        'Gurih' => 0,
        'Pedas Manis' => 0,
        'Opsional' => 0
    ];
    
    // No pedas levels (feature removed)

    // Ambil data minuman dari database jika ada
    $drinks = [];
    
    if (isset($conn) && $conn && !isset($conn->connect_error)) {
        $table_check = @$conn->query("SHOW TABLES LIKE 'menu'");
        if ($table_check && $table_check->num_rows > 0) {
            $sql = "SELECT * FROM menu WHERE category = 'drink' ORDER BY id";
            $result = @$conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $drinks[] = $row;
                }
            }
        }
    }
    
    // Jika tidak ada minuman di database, gunakan data dummy
    if (empty($drinks)) {
        $drinks = [
            [
                'id' => 1,
                'name' => 'Es Jeruk',
                'price' => 8000,
                'image' => 'assets/seblak1.jpeg'
            ],
            [
                'id' => 2,
                'name' => 'Es Teh Manis',
                'price' => 5000,
                'image' => 'assets/seblak2.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Es Campur',
                'price' => 12000,
                'image' => 'assets/seblak1.jpeg'
            ],
            [
                'id' => 4,
                'name' => 'Jus Alpukat',
                'price' => 10000,
                'image' => 'assets/seblak2.jpg'
            ]
        ];
    }
?>

<section class="full-menu-list">
    <h2>Seblak Prasmanan</h2>
    <p>Pilih topping favorit Anda untuk membuat seblak sesuai selera!</p>
    
    <!-- Section Topping -->
    <section class="menu-category-section" id="toppings">
        <h3>Pilihan Topping</h3>
        
        <div class="topping-grid">
            <?php foreach ($toppings as $topping): ?>
                <div class="topping-item" data-topping-id="<?php echo $topping['id']; ?>">
                    <div class="topping-image">
                        <img src="<?php echo htmlspecialchars($topping['image']); ?>" alt="<?php echo htmlspecialchars($topping['name']); ?>" loading="lazy" onerror="this.src='assets/seblak1.jpeg'">
                        <div class="topping-checkbox-overlay">
                            <input type="checkbox" 
                                   name="topping[]" 
                                   value="<?php echo htmlspecialchars($topping['name']); ?>" 
                                   id="topping-<?php echo $topping['id']; ?>"
                                   data-price="<?php echo $topping['price']; ?>"
                                   data-name="<?php echo htmlspecialchars($topping['name']); ?>"
                                   onchange="toggleToppingQuantity(this)">
                            <label for="topping-<?php echo $topping['id']; ?>" class="topping-check-label">
                                <span class="check-icon">✓</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="topping-info">
                        <h4><?php echo htmlspecialchars($topping['name']); ?></h4>
                        <span class="topping-price">Rp <?php echo number_format($topping['price'], 0, ',', '.'); ?></span>
                        <span class="topping-stock">Stok: <?php echo htmlspecialchars($topping['stock']); ?></span>
                        
                        <div class="topping-quantity" style="display: none;">
                            <label>Jumlah:</label>
                            <div class="quantity-control-small">
                                <button type="button" class="qty-btn-small minus" onclick="changeToppingQuantity(this, -1)">-</button>
                                <input type="number" 
                                       class="topping-qty-input" 
                                       value="0" 
                                       min="0" 
                                       max="<?php echo $topping['stock']; ?>"
                                       data-topping-id="<?php echo $topping['id']; ?>"
                                       data-price="<?php echo $topping['price']; ?>"
                                       data-stock="<?php echo $topping['stock']; ?>"
                                       onchange="validateToppingQuantity(this); updateTotal()">
                                <button type="button" class="qty-btn-small plus" onclick="changeToppingQuantity(this, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Section Minuman -->
    <?php if (!empty($drinks)): ?>
    <section class="menu-category-section" id="drinks" style="margin-top: 40px;">
        <h3 style="font-size: 28px; margin-bottom: 20px;">🍹 Menu Minuman</h3>
        
        <div class="topping-grid">
            <?php foreach ($drinks as $drink): ?>
                <div class="drink-item">
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($drink['image']); ?>" alt="<?php echo htmlspecialchars($drink['name']); ?>" loading="lazy" onerror="this.src='assets/seblak1.jpeg'">
                    </div>
                    
                    <div class="drink-info">
                        <h3><?php echo htmlspecialchars($drink['name']); ?></h3>
                        <span class="price">Rp <?php echo number_format($drink['price'], 0, ',', '.'); ?></span>

                        <form class="menu-order-form" data-base-price="<?php echo $drink['price']; ?>">
                            <div class="form-group">
                                <label>Jumlah:</label>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn minus" onclick="changeQuantityDrink(this, -1)">-</button>
                                    <input type="number" name="quantity" value="1" min="1" class="quantity-input" onchange="calculateDrinkTotal(this)">
                                    <button type="button" class="qty-btn plus" onclick="changeQuantityDrink(this, 1)">+</button>
                                </div>
                            </div>

                            <div class="price-summary">
                                <div class="price-row total-row">
                                    <span>Total:</span>
                                    <span class="total-price">Rp <?php echo number_format($drink['price'], 0, ',', '.'); ?></span>
                                </div>
                            </div>

                            <button type="button" class="add-to-cart-btn" onclick="addDrinkToCart(this)">Tambah ke Keranjang</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Form Pemesanan - Pilihan Kuah -->
    <section class="order-form-section" style="margin-top: 40px;">
        <div class="order-form-container">
            <h3>Pilihan Kuah</h3>
            
            <form class="seblak-order-form" id="seblakOrderForm">
                <div class="form-group">
                    <label>Pilihan Kuah:</label>
                    <select name="kuah" id="kuahSelect" class="kuah-select" onchange="updateTotal()">
                        <?php foreach ($kuah_options as $kuah_name => $kuah_price): ?>
                            <option value="<?php echo htmlspecialchars($kuah_name); ?>" data-price="<?php echo $kuah_price; ?>">
                                <?php echo htmlspecialchars($kuah_name); ?><?php echo $kuah_price > 0 ? ' (+Rp ' . number_format($kuah_price, 0, ',', '.') . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Level pedas removed per request -->

                <div class="price-summary">
                    <div class="price-row">
                        <span>Total Topping:</span>
                        <span id="toppingTotal">Rp 0</span>
                    </div>
                    <!-- Pedas price row removed -->
                    <div class="price-row">
                        <span>Total Minuman:</span>
                        <span id="drinksTotal">Rp 0</span>
                    </div>
                    <div class="price-row total-row">
                        <span>Total Harga:</span>
                        <span id="grandTotal">Rp 0</span>
                    </div>
                </div>

                <button type="button" class="add-to-cart-btn" onclick="addPortion()">Tambah Porsi</button>
                <button type="button" class="secondary-btn" onclick="goToCheckout()" id="checkoutBtn" style="display: none; margin-top: 10px; width: 100%; padding: 14px; background: white; color: #FF6B35; border: 2px solid #FF6B35; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">Checkout</button>
            </form>
        </div>
    </section>

    <!-- Cart Summary -->
    <div id="cartSummary" style="display: none; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 30px;">
        <h3 style="margin-bottom: 15px;">Ringkasan Porsi</h3>
        <div id="portionsList"></div>
    </div>
    
    <!-- Drinks Summary -->
    <div id="drinksSummary" style="display: none; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 30px;"></div>

</section>

<script>
// Fungsi untuk mengubah jumlah topping
function changeToppingQuantity(btn, change) {
    const toppingItem = btn.closest('.topping-item');
    const input = toppingItem.querySelector('.topping-qty-input');
    const checkbox = toppingItem.querySelector('input[type="checkbox"]');
    
    const maxStock = parseInt(input.dataset.stock) || 0;
    let currentValue = parseInt(input.value) || 0;
    let newValue = currentValue + change;
    
    if (newValue < 0) newValue = 0;
    if (newValue > maxStock) {
        newValue = maxStock;
        alert('Jumlah melebihi stok tersedia! Stok tersedia: ' + maxStock);
    }
    
    // Jika jumlah menjadi lebih dari 0, pastikan checkbox dicentang dan quantity div ditampilkan
    if (newValue > 0) {
        if (!checkbox.checked) {
            checkbox.checked = true;
            toppingItem.querySelector('.topping-quantity').style.display = 'block';
        }
    }
    
    // Jika jumlah menjadi 0, uncheck checkbox dan sembunyikan quantity div
    if (newValue === 0) {
        if (checkbox.checked) {
            checkbox.checked = false;
            toppingItem.querySelector('.topping-quantity').style.display = 'none';
        }
    }
    
    input.value = newValue;
    updateTotal();
}

// Fungsi untuk validasi jumlah topping saat input manual
function validateToppingQuantity(input) {
    const maxStock = parseInt(input.dataset.stock) || 0;
    let currentValue = parseInt(input.value) || 0;
    
    if (currentValue < 0) {
        input.value = 0;
        currentValue = 0;
    }
    
    if (currentValue > maxStock) {
        input.value = maxStock;
        alert('Jumlah melebihi stok tersedia! Stok tersedia: ' + maxStock);
    }
    
    // Update checkbox dan visibility
    const toppingItem = input.closest('.topping-item');
    const checkbox = toppingItem.querySelector('input[type="checkbox"]');
    const quantityDiv = toppingItem.querySelector('.topping-quantity');
    
    if (currentValue > 0) {
        if (!checkbox.checked) {
            checkbox.checked = true;
            quantityDiv.style.display = 'block';
        }
    } else {
        if (checkbox.checked) {
            checkbox.checked = false;
            quantityDiv.style.display = 'none';
        }
    }
}

// Fungsi untuk menghitung total harga seblak
function updateTotal() {
    // Hitung total topping berdasarkan jumlah
    let toppingTotal = 0;
    const toppingInputs = document.querySelectorAll('.topping-qty-input');
    const selectedToppings = [];
    
    toppingInputs.forEach(function(input) {
        const quantity = parseInt(input.value) || 0;
        if (quantity > 0) {
            const price = parseFloat(input.dataset.price) || 0;
            const toppingItem = input.closest('.topping-item');
            const toppingName = toppingItem.querySelector('h4').textContent;
            const itemTotal = price * quantity;
            toppingTotal += itemTotal;
            selectedToppings.push(toppingName + ' (x' + quantity + ')');
        }
    });
    
    // Hitung kuah
    const kuahSelect = document.getElementById('kuahSelect');
    const kuahOption = kuahSelect.options[kuahSelect.selectedIndex];
    const kuahPrice = parseFloat(kuahOption.dataset.price) || 0;
    
    // Pedas removed — no pedasPrice
    
    // Hitung total minuman
    let drinksTotal = 0;
    drinks.forEach(function(drink) {
        drinksTotal += drink.total;
    });
    
    // Hitung total
    const grandTotal = toppingTotal + kuahPrice + drinksTotal;
    
    // Update display
    document.getElementById('toppingTotal').textContent = 'Rp ' + formatNumber(toppingTotal);
    // pedasTotal removed
    document.getElementById('drinksTotal').textContent = 'Rp ' + formatNumber(drinksTotal);
    
    document.getElementById('grandTotal').textContent = 'Rp ' + formatNumber(grandTotal);
}

// Fungsi untuk format angka
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Array untuk menyimpan porsi
let portions = [];

// Array untuk menyimpan minuman
let drinks = [];

// Fungsi untuk menambah porsi
function addPortion() {
    const toppingInputs = document.querySelectorAll('.topping-qty-input');
    const selectedToppings = [];
    
    toppingInputs.forEach(function(input) {
        const quantity = parseInt(input.value) || 0;
        if (quantity > 0) {
            const toppingItem = input.closest('.topping-item');
            const toppingId = toppingItem.dataset.toppingId;
            const toppingName = toppingItem.querySelector('h4').textContent;
            const toppingPrice = parseFloat(input.dataset.price) || 0;
            
            selectedToppings.push({
                id: toppingId,
                name: toppingName,
                price: toppingPrice,
                quantity: quantity
            });
        }
    });
    
    if (selectedToppings.length === 0) {
        alert('Silakan pilih minimal satu topping!');
        return;
    }
    
    const kuahSelect = document.getElementById('kuahSelect');
    const kuah = kuahSelect.value;
    const kuahOption = kuahSelect.options[kuahSelect.selectedIndex];
    const kuahPrice = parseFloat(kuahOption.dataset.price) || 0;
    
    // Calculate total for this portion (no pedas)
    let portionTotal = kuahPrice;
    selectedToppings.forEach(function(topping) {
        portionTotal += topping.price * topping.quantity;
    });
    
    // Add portion to array
    portions.push({
        toppings: selectedToppings,
        kuah: kuah,
        total: portionTotal
    });
    
    // Save to session via AJAX
    savePortionsToSession();
    
    // Update UI
    updateCartSummary();
    
    // Reset form
    resetForm();
    
    alert('Porsi ' + portions.length + ' berhasil ditambahkan!');
}

// Fungsi untuk reset form
function resetForm() {
    // Uncheck all toppings
    document.querySelectorAll('.topping-qty-input').forEach(function(input) {
        const checkbox = input.closest('.topping-item').querySelector('input[type="checkbox"]');
        checkbox.checked = false;
        input.value = 0;
        input.closest('.topping-quantity').style.display = 'none';
    });
    
    // Reset selects
    document.getElementById('kuahSelect').selectedIndex = 0;
    
    updateTotal();
}

// Fungsi untuk update cart summary
function updateCartSummary() {
    const cartSummary = document.getElementById('cartSummary');
    const portionsList = document.getElementById('portionsList');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (portions.length > 0) {
        cartSummary.style.display = 'block';
        checkoutBtn.style.display = 'block';
        
        let html = '';
        portions.forEach(function(portion, index) {
            html += '<div style="margin-bottom: 15px; padding: 15px; background: #FFF5F0; border-radius: 8px;">';
            html += '<strong>Porsi ' + (index + 1) + '</strong><br>';
            html += 'Topping: ' + portion.toppings.map(t => t.name + ' x' + t.quantity).join(', ') + '<br>';
            html += 'Kuah: ' + portion.kuah + '<br>';
            html += '<strong style="color: #FF6B35;">Rp ' + formatNumber(portion.total) + '</strong>';
            html += '<button onclick="removePortion(' + index + ')" style="float: right; padding: 5px 10px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">Hapus</button>';
            html += '</div>';
        });
        
        const grandTotal = portions.reduce((sum, p) => sum + p.total, 0);
        html += '<div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #FF6B35; display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: #FF6B35;">';
        html += '<span>Total Semua Porsi:</span>';
        html += '<span>Rp ' + formatNumber(grandTotal) + '</span>';
        html += '</div>';
        
        portionsList.innerHTML = html;
    } else {
        cartSummary.style.display = 'none';
        checkoutBtn.style.display = 'none';
    }
}

// Fungsi untuk hapus porsi
function removePortion(index) {
    portions.splice(index, 1);
    savePortionsToSession();
    updateCartSummary();
}

// Fungsi untuk save portions ke session
function savePortionsToSession() {
    fetch('save_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ portions: portions })
    });
}

// Fungsi untuk go to checkout
function goToCheckout() {
    if (portions.length === 0) {
        alert('Silakan tambahkan minimal satu porsi!');
        return;
    }
    
    // Save portions and drinks before redirect
    savePortionsToSession();
    saveDrinksToSession();
    
    // Redirect to checkout
    window.location.href = 'checkout.php';
}

// Load portions from session on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.portions) {
                portions = data.portions;
                updateCartSummary();
            }
        });
    updateTotal();
});

// Fungsi untuk minuman
function changeQuantityDrink(btn, change) {
    const form = btn.closest('.menu-order-form');
    const input = form.querySelector('.quantity-input');
    let currentValue = parseInt(input.value) || 1;
    let newValue = currentValue + change;
    
    if (newValue < 1) newValue = 1;
    
    input.value = newValue;
    calculateDrinkTotal(input);
}

function calculateDrinkTotal(element) {
    const form = element.closest('.menu-order-form');
    const basePrice = parseFloat(form.dataset.basePrice) || 0;
    const quantity = parseInt(element.value) || 1;
    const total = basePrice * quantity;
    
    form.querySelector('.total-price').textContent = 'Rp ' + formatNumber(total);
}

// Fungsi untuk toggle quantity input saat checkbox diubah
function toggleToppingQuantity(checkbox) {
    const toppingItem = checkbox.closest('.topping-item');
    const quantityDiv = toppingItem.querySelector('.topping-quantity');
    const qtyInput = toppingItem.querySelector('.topping-qty-input');
    
    if (checkbox.checked) {
        quantityDiv.style.display = 'block';
        if (parseInt(qtyInput.value) === 0) {
            qtyInput.value = 1;
        }
    } else {
        quantityDiv.style.display = 'none';
        qtyInput.value = 0;
    }
    updateTotal();
}

function addDrinkToCart(btn) {
    const form = btn.closest('.menu-order-form');
    const menuItem = btn.closest('.menu-item');
    const itemName = menuItem.querySelector('h3').textContent;
    const quantity = parseInt(form.querySelector('.quantity-input').value) || 1;
    const basePrice = parseFloat(form.dataset.basePrice) || 0;
    const totalPrice = basePrice * quantity;
    
    // Cek apakah minuman sudah ada di cart
    const existingDrinkIndex = drinks.findIndex(d => d.name === itemName);
    
    if (existingDrinkIndex >= 0) {
        // Update quantity jika sudah ada
        drinks[existingDrinkIndex].quantity += quantity;
        drinks[existingDrinkIndex].total = drinks[existingDrinkIndex].price * drinks[existingDrinkIndex].quantity;
    } else {
        // Tambahkan minuman baru
        drinks.push({
            name: itemName,
            price: basePrice,
            quantity: quantity,
            total: totalPrice
        });
    }
    
    // Save to session
    saveDrinksToSession();
    
    // Update UI
    updateDrinksSummary();
    updateTotal();
    
    // Reset form
    form.querySelector('.quantity-input').value = 1;
    calculateDrinkTotal(form.querySelector('.quantity-input'));
    
    alert('Minuman ditambahkan ke keranjang!\n\n' +
          'Item: ' + itemName + '\n' +
          'Jumlah: ' + quantity + '\n' +
          'Total: ' + 'Rp ' + formatNumber(totalPrice));
}

// Fungsi untuk save drinks ke session
function saveDrinksToSession() {
    fetch('save_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ drinks: drinks })
    });
}

// Fungsi untuk update drinks summary
function updateDrinksSummary() {
    const drinksSummary = document.getElementById('drinksSummary');
    if (!drinksSummary) return;
    
    if (drinks.length === 0) {
        drinksSummary.style.display = 'none';
        return;
    }
    
    drinksSummary.style.display = 'block';
    let html = '<h4 style="margin-bottom: 15px; color: #FF6B35;">Minuman yang Dipilih:</h4>';
    let drinksTotal = 0;
    
    drinks.forEach(function(drink, index) {
        drinksTotal += drink.total;
        html += '<div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f9f9f9; border-radius: 8px; margin-bottom: 10px;">';
        html += '<div>';
        html += '<strong>' + drink.name + '</strong> x' + drink.quantity;
        html += '</div>';
        html += '<div style="display: flex; align-items: center; gap: 15px;">';
        html += '<span style="color: #FF6B35; font-weight: 600;">Rp ' + formatNumber(drink.total) + '</span>';
        html += '<button type="button" onclick="removeDrink(' + index + ')" style="background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;">Hapus</button>';
        html += '</div>';
        html += '</div>';
    });
    
    html += '<div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #FFF5F0; border-radius: 8px; margin-top: 15px; border-top: 2px solid #FF6B35;">';
    html += '<strong>Total Minuman:</strong>';
    html += '<strong style="color: #FF6B35; font-size: 18px;">Rp ' + formatNumber(drinksTotal) + '</strong>';
    html += '</div>';
    
    drinksSummary.innerHTML = html;
}

// Fungsi untuk remove drink
function removeDrink(index) {
    drinks.splice(index, 1);
    saveDrinksToSession();
    updateDrinksSummary();
    updateTotal();
}

// Load portions from session on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.portions) {
                portions = data.portions;
                updateCartSummary();
            }
        });
    updateTotal();
});
</script>

<?php
    // Tutup koneksi database setelah selesai (jika masih terbuka)
    if (isset($conn) && $conn) {
        @$conn->close();
    }
    // Menyertakan footer template
    include 'includes/footer.php';
?>
