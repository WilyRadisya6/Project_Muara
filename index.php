<?php 
    // Set page title
    $page_title = 'Home';
    // Menyertakan header template
    include 'includes/header.php'; 
?>


<section class="hero">
    <div class="hero-decorative-shapes">
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>
    </div>
    <div class="hero-content">
        <div class="hero-text">
            <h1>Best food for your taste</h1>
            <p>Discover delectable cuisine and unforgettable moments, in our welcoming, culinary haven.</p>
            <div class="hero-buttons">
                <a href="book_table.php" class="primary-btn">Book A Table</a>
                <a href="menu.php" class="secondary-btn">Explore Menu</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="assets/Yummy`.png" alt="" loading="lazy"> 
        </div>
    </div>
</section>

<section class="info-section">
    <div class="info-image">
        <img src="assets/seblak2.jpg" alt="" loading="lazy" onerror="this.src='assets/seblak1.jpeg'">
    </div>
    <div class="info-text">
        <h2>Nikmatnya Pedas yang Menemani Harimu</h2>
        <p>Hidangan khas Indonesia yang terbuat dari kerupuk kenyal, dimasak dengan bumbu pedas, sayuran, dan berbagai topping, disajikan segar dan lezat untuk menemani hari Anda dan memuaskan hasrat akan makanan lezat dan menghangatkan.</p>
    </div>
</section>

<section class="browse-menu-section">
    <h2>Browse Our Menu</h2>
    <div class="browse-grid">
        <a href="menu.php" class="browse-item">
            <div class="browse-icon">
                <img src="assets/seblak1.jpeg" alt="Seblak Kerupuk" onerror="this.src='assets/seblak2.jpg'">
            </div>
            <p class="browse-item-name">Seblak Kerupuk</p>
        </a>
        <a href="menu.php" class="browse-item">
            <div class="browse-icon">
                <img src="assets/seblak2.jpg" alt="Seblak Mie" onerror="this.src='assets/seblak1.jpeg'">
            </div>
            <p class="browse-item-name">Seblak Mie</p>
        </a>
        <a href="menu.php" class="browse-item">
            <div class="browse-icon">
                <img src="assets/Seblak ceker.jpg" alt="Seblak Ceker" onerror="this.src='assets/seblak1.jpeg'">
            </div>
            <p class="browse-item-name">Seblak Ceker</p>
        </a>
        <a href="menu.php" class="browse-item">
            <div class="browse-icon">
                <img src="assets/0aaa473b85708194053ef42c7ba9941c.jpg" alt="Seblak Komplit" onerror="this.src='assets/seblak2.jpg'">
            </div>
            <p class="browse-item-name">Seblak Komplit</p>
        </a>
    </div>
</section>

<?php
    // Menyertakan footer template
    include 'includes/footer.php';
?>