<?php
session_start();
require './database/db.php';

if (!isset($_SESSION['driver_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    $stmt = $conn->prepare("SELECT id FROM driver WHERE remember_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['driver_id'] = $user['id'];
    } else {
        // Token không hợp lệ hoặc hết hạn
        setcookie('remember_token', '', time() - 3600, '/');
    }
}
?>