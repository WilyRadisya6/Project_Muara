<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (isset($data['portions'])) {
        $_SESSION['portions'] = $data['portions'];
    }
    
    if (isset($data['cart'])) {
        $_SESSION['cart'] = $data['cart'];
    }
    
    if (isset($data['drinks'])) {
        $_SESSION['drinks'] = $data['drinks'];
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>

