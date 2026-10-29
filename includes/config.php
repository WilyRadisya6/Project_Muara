<?php
/**
 * Konfigurasi Template Yummy Yamy
 */

// Base URL (sesuaikan dengan struktur folder Anda)
define('BASE_URL', '/');

// Path untuk assets
define('ASSETS_URL', BASE_URL . 'assets/');

// Path untuk CSS (relatif dari root)
define('CSS_URL', 'style.css');

// Site Information
define('SITE_NAME', 'Yummy Yamy');
define('SITE_TAGLINE', 'Makanan Lezat');

// Fungsi helper untuk mendapatkan active page
function getActivePage() {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page;
}

// Fungsi untuk menentukan apakah link aktif
function isActive($page) {
    return getActivePage() === $page ? 'active' : '';
}

// Fungsi untuk mendapatkan title page
function getPageTitle($page_title = '') {
    if ($page_title) {
        return $page_title . ' - ' . SITE_NAME;
    }
    return SITE_NAME . ' - ' . SITE_TAGLINE;
}
?>

