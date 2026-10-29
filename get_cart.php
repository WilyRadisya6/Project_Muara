<?php
session_start();
header('Content-Type: application/json');

$response = [
    'portions' => isset($_SESSION['portions']) ? $_SESSION['portions'] : [],
    'cart' => isset($_SESSION['cart']) ? $_SESSION['cart'] : [],
    'drinks' => isset($_SESSION['drinks']) ? $_SESSION['drinks'] : []
];

echo json_encode($response);
?>

