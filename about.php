<?php
// About page
$page_title = 'About Us';
include 'includes/header.php';
?>

<section class="hero about-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Tentang Yummy Yamy</h1>
            <p>Yummy Yamy berdiri untuk menghadirkan cita rasa otentik dengan sentuhan modern — hidangan yang menyatukan komunitas dan kenangan.</p>
        </div>
        <div class="hero-image">
            <img src="assets/about-hero.jpg" alt="Yummy Yamy" onerror="this.src='assets/Yummy`.png'">
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <h2>Visi & Misi</h2>
        <p><strong>Visi:</strong> Menjadi tujuan kuliner favorit dengan pelayanan hangat dan rasa yang konsisten.</p>
        <p><strong>Misi:</strong> Menyajikan bahan berkualitas, resep yang terus disempurnakan, dan pengalaman makan yang menyenangkan bagi semua pelanggan.</p>

        <h3 style="margin-top:24px;">Sejarah Singkat</h3>
        <p>Dimulai dari ide sederhana untuk menyatukan resep warisan lokal dengan teknik masak modern, Yummy Yamy tumbuh dari sebuah rumah makan kecil menjadi tempat berkumpul yang dicintai banyak orang.</p>

        <h3 style="margin-top:24px;">Tim Kami</h3>
        <p>Didukung oleh tim kuliner dan layanan yang berdedikasi, kami percaya bahwa setiap hidangan harus dibuat dengan cinta dan perhatian pada detail.</p>

        <div style="margin-top:30px; padding:18px; background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
            <h4>Kontak & Kerjasama</h4>
            <p>Ingin bekerja sama atau memesan dalam jumlah besar? Hubungi kami melalui halaman <a href="contact.php">Contact</a> atau email: <strong>info@yummyyamy.com</strong></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
