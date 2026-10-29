<?php 
    session_start();
    
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        header('Location: login.php');
        exit();
    }
    
    include '../includes/database.php';
    
    if (!isset($_GET['id'])) {
        header('Location: dashboard.php');
        exit();
    }
    
    $user_id = intval($_GET['id']);
    $admin = new Admin($conn);
    
    $result = $admin->deleteUser($user_id);
    
    $_SESSION['message'] = $result['message'];
    $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
    
    header('Location: dashboard.php');
    exit();
?>

