<?php
// db_connect.php
$servername = "localhost";
$username = "root"; // Ganti jika username database Anda berbeda
$password = "";     // Ganti jika password database Anda berbeda
$dbname = "yummy_yamy_db"; // PASTIKAN NAMA DATABASE INI SESUAI

// Buat Koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
 
}
