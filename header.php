<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yummy Yamy - Makanan Lezat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="logo">Yummy Yamy</div>
        <nav class="nav">
            <a href="index.php">Beranda</a>
            <a href="menu.php">Menu</a>
            <a href="#">Tentang Kami</a>
            <a href="#">Kontak</a>
        </nav>
    </header>
    
    ```

### 2. File `includes/footer.php`

File ini akan menutup tag dokumen HTML dan berisi *footer*.

```php
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Yummy Yamy. Semua Hak Dilindungi.</p>
        <div class="social-links">
            <a href="#">[Instagram]</a>
            <a href="#">[Facebook]</a>
        </div>
    </footer>

</body>
</html>