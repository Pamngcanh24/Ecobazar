<?php 
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit();
}
// Kết nối DB
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
